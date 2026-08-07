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
			view('403');
			exit;
		}

		$assetRequest = $this->assetRequestModel->findOrFail($assetRequestId);

		$asset = $this->assetModel->find($assetRequest['asset_id']);

		if ($assetRequest['status'] === 'RETURNED' || $assetRequest['status'] === 'CANCELLED') {
			$message = "This asset/request has been $assetRequest[status]";
			view('403', [
				'message' => $message
			]);
			exit;
		}

		$statusEnum = $this->getStatus();

		view('asset.requests.manage', ['statusEnum' => $statusEnum]);
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

	public function update(int $requestId, array $data)
	{
		$assetRequest = $this->assetRequestModel->findOrFail($requestId);
		$errors = $this->validate($data);

		if (!empty($errors)) {
			$statusEnum = $this->getStatus();
			view('asset.requests.manage');
			exit;
		}

		$data = $this->normalize($data);

		if ($errors['general'] = $this->validateStatus($assetRequest, $data)) {
			$statusEnum = $this->getStatus();
			view('asset.requests.manage');
			exit;
		}

		$this->assetRequestModel->update($requestId, $data);

		if ($data['status'] === 'APPROVED') {
			(new Asset($this->conn))->updateStatus($assetRequest['asset_id'], 'ASSIGNED');
		} elseif ($data['status'] === 'RETURNED') {
			(new Asset($this->conn))->updateStatus($assetRequest['asset_id'], 'AVAILABLE');
		}

		$_SESSION['success'] = 'Asset request updated successfully';
		route('assets/requests');
	}

	private function validate(array $assetRequest): array
	{
		$errors = [];

		$status = $assetRequest['status'];
		$remark = $assetRequest['remark'];
		$statusEnum = $this->getStatus();

		if (empty($status)) {
			$errors['status'] = 'Status is required';
		}

		if (!in_array(strtoupper($status), $statusEnum)) {
			$errors['status'] = 'Status is not valid';
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

			'rejection_reason' => empty($request['rejection_reason']) ? null : $request['remark'],

			'issued_by' => $request['status'] === 'ISSUED' ? $_SESSION['user_id'] : null,
			'issued_at' => $request['status'] === 'ISSUED' ? date('Y-m-d H:i:s') : null,

			'remark' => empty($request['remark']) ? null : $request['remark'],
			'returned_at' => $request['status'] === 'RETURNED' ? date('Y-m-d H:i:s') : null,
		];
	}

	private function validateStatus(array $assetRequest, array $input)
	{
		$assetStatus = $assetRequest['status'];
		$inputStatus = $input['status'];

		switch ($assetStatus) {
			case 'REJECTED':
				if ($inputStatus !== 'REJECTED') {
					return 'This request is rejected.';
				}
				break;

			case 'RETURNED':
				if ($inputStatus !== 'RETURNED') {
					return 'This asset is returned.';
				}
				break;

			case 'CANCELLED':
				if ($inputStatus !== 'CANCELLED') {
					return 'This request is already cancelled.';
				}
				break;
		}

		return null;
	}

	private function alreadyAssigned($assetRequest): bool
	{
		$stmt = $this->conn->prepare('select * from asset_requests 
         where asset_id = :asset_id 
           and user_id != :user_id
           and status in ("APPROVED", "ISSUED")
         LIMIT 1
        ');

		$stmt->execute([
			'asset_id' => $assetRequest['asset_id'],
			'user_id' => $assetRequest['user_id'],
		]);

		return $stmt->rowCount() === 1;
	}
}