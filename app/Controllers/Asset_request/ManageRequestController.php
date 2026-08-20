<?php

namespace App\Controllers\Asset_request;

use App\helpers\Csrf;
use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\User;

class ManageRequestController
{
	private \PDO $conn;
	private AssetRequest $assetRequestModel;
	private Asset $assetModel;

	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->assetRequestModel = new AssetRequest($conn);
		$this->assetModel = new Asset($conn);
	}

	/* =========================================================
	 * PUBLIC ACTIONS
	 * ========================================================= */

	public function showManageForm(int $assetRequestId)
	{
		middleware('manager');

		$assetRequest = $this->assetRequestModel->findOrFail($assetRequestId);

		if (
			$assetRequest['status'] === 'RETURNED'
			|| $assetRequest['status'] === 'CANCELLED'
		) {
			view(403, [
				'message' => "This asset/request has been $assetRequest[status]"
			]);
			exit;
		}

		$asset = $this->assetModel->find($assetRequest['asset_id']);

		view('asset.requests.manage', [
			'assetRequest' => $assetRequest,
			'statusEnum' => $this->getStatus(),
			'asset' => $asset,
			'warrantyWarning' => $this->getWarrantyWarning(
				$asset['warranty_date'] ?? null
			),
		]);
	}

	public function update(int $requestId, array $inputAssetRequest)
	{
		if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
			view(403);
			exit;
		}

		$assetRequest = $this->assetRequestModel->findOrFail($requestId);
		$asset = $this->assetModel->find($assetRequest['asset_id']);

		$errors = $this->validate($inputAssetRequest);

		if (!empty($errors)) {
			$this->renderManageForm($assetRequest, $asset, $errors);
			exit;
		}

		$inputAssetRequest = $this->normalize($inputAssetRequest);

		if (
			$generalError = $this->alreadyAssigned(
				$asset,
				$assetRequest,
				$inputAssetRequest
			)
		) {
			$this->renderManageForm(
				$assetRequest,
				$asset,
				['general' => $generalError]
			);
			exit;
		}

		if (
			$generalError = $this->validateStatus(
				$assetRequest,
				$inputAssetRequest
			)
		) {
			$this->renderManageForm(
				$assetRequest,
				$asset,
				['general' => $generalError]
			);
			exit;
		}

		// A deleted employee cannot have an asset request approved or issued.
		if (
			in_array(
				$inputAssetRequest['status'],
				['APPROVED', 'ISSUED'],
				true
			)
		) {
			$user = (new User($this->conn))
				->find($assetRequest['user_id'])[0];

			if ($user['deleted_at']) {
				if ($inputAssetRequest['status'] === 'ISSUED') {
					$stmt = $this->conn->prepare(
						'update assets set status = "AVAILABLE" where id = ?'
					);
					$stmt->execute([$asset['id']]);
				}

				$this->renderManageForm(
					$assetRequest,
					$asset,
					[
						'general' =>
						'Cannot approve/issue this request because the employee has been deleted.'
					]
				);
				exit;
			}
		}

		$this->assetRequestModel->update(
			$requestId,
			$inputAssetRequest,
			$assetRequest
		);

		$this->applyStatusSideEffects(
			$inputAssetRequest['status'],
			$assetRequest,
			$asset
		);

		$_SESSION['success'] = 'Asset request updated successfully.';
		route('assets/requests');
	}

	public function cancel()
	{
		middleware('auth');

		if (empty($_GET['id'])) {
			view(404);
			exit;
		}

		$id = (int) $_GET['id'];
		$assetRequest = $this->assetRequestModel->findOrFail($id);

		middleware('assetOwner', [
			'assetRequest' => $assetRequest,
		]);

		$requestStatus = $assetRequest['status'];

		if (
			in_array(
				$requestStatus,
				['CANCELLED', 'REJECTED', 'ISSUED', 'RETURNED'],
				true
			)
		) {
			view(404);
			exit;
		}

		$this->assetRequestModel->update(
			$assetRequest['id'],
			['status' => 'CANCELLED'],
			$assetRequest
		);

		if ($requestStatus === 'APPROVED') {
			$this->assetModel->updateStatus(
				$assetRequest['asset_id'],
				'AVAILABLE',
				null
			);
		}

		$_SESSION['success'] = 'Request cancelled successfully';
		route('assets/requests');
		exit;
	}

	/* =========================================================
	 * RENDER HELPERS
	 * ========================================================= */

	private function renderManageForm(
		array $assetRequest,
		array $asset,
		array $errors
	): void {
		view('asset.requests.manage', [
			'assetRequest' => $assetRequest,
			'asset' => $asset,
			'errors' => $errors,
			'statusEnum' => $this->getStatus(),
			'warrantyWarning' => $this->getWarrantyWarning(
				$asset['warranty_date'] ?? null
			),
		]);
	}

	/* =========================================================
	 * STATUS SIDE EFFECTS
	 * ========================================================= */

	private function applyStatusSideEffects(
		string $updatedStatus,
		array $assetRequest,
		array $asset
	): void {
		// Do not create duplicate notices when an administrator saves the same status.
		if (($assetRequest['status'] ?? null) !== $updatedStatus) {
			sendStatusNotice($updatedStatus, [
				'user_id' => $assetRequest['user_id'],
				'asset_name' => $assetRequest['asset_name'] ?? $asset['name'] ?? 'the requested asset',
			], $this->conn);
		}

		switch ($updatedStatus) {
			case 'APPROVED':
				(new Asset($this->conn))->updateStatus(
					$assetRequest['asset_id'],
					'ASSIGNED',
					$assetRequest['user_id']
				);
				break;

			case 'RETURNED':
				(new AuditLog($this->conn))->log(
					'ASSET_RETURN',
					$assetRequest['asset_id']
				);

				(new Asset($this->conn))->updateStatus(
					$assetRequest['asset_id'],
					'AVAILABLE'
				);
				break;

			case 'ISSUED':
				(new AuditLog($this->conn))->log(
					'ASSET_ASSIGNMENT',
					$asset['id'],
					$asset['assignee_id']
				);
				break;

		}
	}

	/* =========================================================
	 * STATUS & WARRANTY
	 * ========================================================= */

	private function getStatus(): array
	{
		return [
			'PENDING',
			'APPROVED',
			'REJECTED',
			'ISSUED',
			'RETURNED',
		];
	}

	private function getWarrantyWarning(?string $warrantyDate): ?array
	{
		if (empty($warrantyDate)) {
			return null;
		}

		$today = new \DateTime('today');
		$wDate = new \DateTime($warrantyDate);
		$diff = $today->diff($wDate);
		$daysRemaining = (int) $diff->format('%r%a');

		if ($daysRemaining < 0) {
			$absDays = abs($daysRemaining);

			return [
				'type' => 'expired',
				'days' => $absDays,
				'date' => $warrantyDate,
				'message' =>
				"Warranty expired {$absDays} day" .
					($absDays === 1 ? '' : 's') .
					" ago on " .
					date('M d, Y', strtotime($warrantyDate)) .
					"."
			];
		}

		if ($daysRemaining <= 15) {
			return [
				'type' => 'expiring_soon',
				'days' => $daysRemaining,
				'date' => $warrantyDate,
				'message' => $daysRemaining === 0
					? "Warranty expires today (" .
					date('M d, Y', strtotime($warrantyDate)) .
					")!"
					: "Warranty is expiring in {$daysRemaining} day" .
					($daysRemaining === 1 ? '' : 's') .
					" (on " .
					date('M d, Y', strtotime($warrantyDate)) .
					")."
			];
		}

		return null;
	}

	/* =========================================================
	 * VALIDATION
	 * ========================================================= */

	private function validate(array $assetRequest): array
	{
		$errors = [];
		$status = $assetRequest['status'] ?? '';

		if (empty($status)) {
			$errors['status'] = 'Status is required.';
		} elseif (
			!in_array(
				strtoupper($status),
				$this->getStatus(),
				true
			)
		) {
			$errors['status'] = 'Status is not valid.';
		}

		return $errors;
	}

	private function alreadyAssigned(
		array $asset,
		array $assetRequest,
		array $inputAssetRequest
	): ?string {
		if (
			$asset['status'] === 'ASSIGNED'
			&& in_array(
				$inputAssetRequest['status'],
				['APPROVED', 'ISSUED', 'RETURNED'],
				true
			)
			&& $assetRequest['user_id'] !== $asset['assignee_id']
		) {
			return "This asset is already been assigned to #{$asset['assignee_id']} {$asset['user_name']}.";
		}

		return null;
	}

	private function validateStatus(
		array $assetRequest,
		array $input
	): ?string {
		$assetRequestStatus = $assetRequest['status'];
		$inputStatus = $input['status'];

		switch ($assetRequestStatus) {
			// A pending request cannot be issued or returned directly.
			case 'PENDING':
				if ($inputStatus === 'ISSUED') {
					return 'This asset request is not approved yet.';
				}

				if ($inputStatus === 'RETURNED') {
					return 'This asset is not issued yet.';
				}

				break;

			// An approved request cannot be moved back to pending/rejected
			// and cannot be returned before being issued.
			case 'APPROVED':
				if (
					in_array(
						$inputStatus,
						['PENDING', 'REJECTED'],
						true
					)
				) {
					return 'This asset request has been approved.';
				}

				if ($inputStatus === 'RETURNED') {
					return 'This asset has not issued yet.';
				}

				break;

			case 'ISSUED':
				if (
					in_array(
						$inputStatus,
						['PENDING', 'APPROVED', 'REJECTED'],
						true
					)
				) {
					return 'This asset request is already issued';
				}

				break;

			case 'REJECTED':
				if ($inputStatus !== 'REJECTED') {
					return 'This request is already rejected.';
				}

				break;

			case 'OVERDUE':
				if ($inputStatus !== 'RETURNED') {
					return 'This request is already issued.';
				}

				break;
		}

		return null;
	}

	/* =========================================================
	 * DATA NORMALIZATION
	 * ========================================================= */

	private function normalize(array $request): array
	{
		return [
			'status' => $request['status'],

			'approved_by' =>
			$request['status'] === 'APPROVED'
				? $_SESSION['user_id']
				: null,

			'approved_at' =>
			$request['status'] === 'APPROVED'
				? date('Y-m-d H:i:s')
				: null,

			'rejected_by' =>
			$request['status'] === 'REJECTED'
				? $_SESSION['user_id']
				: null,

			'rejected_at' =>
			$request['status'] === 'REJECTED'
				? date('Y-m-d H:i:s')
				: null,

			'rejection_reason' =>
			empty($request['rejection_reason'])
				? null
				: $request['rejection_reason'],

			'issued_by' =>
			$request['status'] === 'ISSUED'
				? $_SESSION['user_id']
				: null,

			'issued_at' =>
			$request['status'] === 'ISSUED'
				? date('Y-m-d H:i:s')
				: null,

			'remark' =>
			empty($request['remark'])
				? null
				: $request['remark'],

			'returned_at' =>
			$request['status'] === 'RETURNED'
				? date('Y-m-d H:i:s')
				: null,
		];
	}
}
