<?php

namespace App\Controllers\Asset_request;

use App\Models\Asset;
use App\Models\AssetRequest;

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

	public function showManageForm(int $assetRequestId)
	{
		$role = $_SESSION['user_role'] ?? 'EMPLOYEE';

		if ($role === 'EMPLOYEE' || $role === 'HR') {
			view(403);
			exit;
		}

		$assetRequest = $this->assetRequestModel->findOrFail($assetRequestId);

		if ($assetRequest['status'] === 'RETURNED' || $assetRequest['status'] === 'CANCELLED') {
			$message = "This asset/request has been $assetRequest[status]";
			view(403, [
				'message' => $message
			]);
			exit;
		}

		$statusEnum = $this->getStatus();

		view('asset.requests.manage', [
			'assetRequest' => $assetRequest,
			'statusEnum' => $statusEnum
		]);
	}

	private function getStatus(): array
	{
		return [
			'PENDING',
			'APPROVED',
			'REJECTED',
			'ISSUED',
			'RETURNED'
		];
	}

	public function update(int $requestId, array $inputAssetRequest)
	{
		$assetRequest = $this->assetRequestModel->findOrFail($requestId);
		$errors = $this->validate($inputAssetRequest);
		$statusEnum = $this->getStatus();

		if (!empty($errors)) {
			view('asset.requests.manage', [
				'errors' => $errors,
				'statusEnum' => $statusEnum,
				'assetRequest' => $assetRequest,
			]);
			exit;
		}

		$inputAssetRequest = $this->normalize($inputAssetRequest);
		$asset = $this->assetModel->findOrFail($assetRequest['asset_id']);

		if ($errors['general'] = $this->alreadyAssigned($asset, $assetRequest, $inputAssetRequest)) {
			view('asset.requests.manage', [
				'assetRequest' => $assetRequest,
				'errors' => $errors,
				'statusEnum' => $statusEnum,
			]);
			exit;
		}

		if ($errors['general'] = $this->validateStatus($assetRequest, $inputAssetRequest)) {
			view('asset.requests.manage', [
				'assetRequest' => $assetRequest,
				'errors' => $errors,
				'statusEnum' => $statusEnum,
			]);
			exit;
		}

		$this->assetRequestModel->update($requestId, $inputAssetRequest, $assetRequest);

		if ($inputAssetRequest['status'] === 'APPROVED') {
			(new Asset($this->conn))->updateStatus($assetRequest['asset_id'], 'ASSIGNED', $assetRequest['user_id']);
		} elseif ($inputAssetRequest['status'] === 'RETURNED') {
			(new Asset($this->conn))->updateStatus($assetRequest['asset_id'], 'AVAILABLE');
		}
		$_SESSION['success'] = 'Asset request updated successfully.';
		route('assets/requests');
	}

	private function validate(array $assetRequest): array
	{
		$errors = [];

		$status = $assetRequest['status'];
		$statusEnum = $this->getStatus();

		if (empty($status)) {
			$errors['status'] = 'Status is required.';
		}

		if (!in_array(strtoupper($status), $statusEnum)) {
			$errors['status'] = 'Status is not valid.';
		}

		return $errors;
	}

	private function normalize(array $request): array
	{
		return [
			'status' => $request['status'],

			'approved_by' => $request['status'] === 'APPROVED' ? $_SESSION['user_id'] : null,
			'approved_at' => $request['status'] === 'APPROVED' ? date('Y-m-d H:i:s') : null,

			'rejected_by' => $request['status'] === 'REJECTED' ? $_SESSION['user_id'] : null,
			'rejected_at' => $request['status'] === 'REJECTED' ? date('Y-m-d H:i:s') : null,

			'rejection_reason' => empty($request['rejection_reason']) ? null : $request['rejection_reason'],

			'issued_by' => $request['status'] === 'ISSUED' ? $_SESSION['user_id'] : null,
			'issued_at' => $request['status'] === 'ISSUED' ? date('Y-m-d H:i:s') : null,

			'remark' => empty($request['remark']) ? null : $request['remark'],
			'returned_at' => $request['status'] === 'RETURNED' ? date('Y-m-d H:i:s') : null,
		];
	}

	private function alreadyAssigned($asset, $assetRequest, $inputAssetRequest)
	{
		switch ($asset['status']) {
			case 'ASSIGNED':
				switch ($inputAssetRequest['status']) {
					case 'APPROVED':
					case 'ISSUED':
					case 'RETURNED':
						if ($assetRequest['user_id'] !== $asset['assignee_id'])
							return "This asset is already been assigned to #{$asset['assignee_id']} {$asset['user_name']}.";
				}
				break;
		}
		return null;
	}

	private function validateStatus(array $assetRequest, array $input)
	{
		$assetStatus = $assetRequest['status'];
		$inputStatus = $input['status'];

		switch ($assetStatus) {
			//				can approve/reject only
			case 'PENDING':
				switch ($inputStatus) {
					case 'ISSUED':
						return 'This asset request is not approved yet.';
					case 'RETURNED':
						return 'This asset is not issued yet.';
				}
				break;

			//				can issue only
			case 'APPROVED':
				switch ($inputStatus) {
					case 'PENDING':
					case 'REJECTED':
						return 'This asset request has been approved.';
					case 'RETURNED':
						return 'This asset has not issued yet.';
				}
				break;

			case 'ISSUED':
				switch ($inputStatus) {
					case 'PENDING':
					case 'APPROVED':
					case 'REJECTED':
						return 'This asset request is already issued';
				}
				break;

			case 'REJECTED':
				if ($inputStatus !== 'REJECTED') {
					return 'This request is already rejected.';
				}
				break;
		}

		return null;
	}
}
