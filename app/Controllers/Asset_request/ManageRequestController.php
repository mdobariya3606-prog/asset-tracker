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

	public function show(int $id)
	{
		$role = $_SESSION['user_role'] ?? 'EMPLOYEE';

		if ($role === 'EMPLOYEE' || $role === 'HR') {
			require '../resources/view/errors/403.php';
			exit;
		}

		$assetRequest = $this->assetRequestModel->find($id);
		$asset = $this->assetModel->find($assetRequest['asset_id']);

		$statusEnum = $this->getStatus();

		require '../resources/views/asset_requests/manage.php';
	}

	private function getStatus(): array
	{
		return [
			'PENDING',
			'APPROVED',
			'REJECTED',
			'ISSUED',
			'RETURNED',
			'CANCELLED',
		];
	}

	public function update(int $requestId, array $data)
	{
		$assetRequest = $this->assetRequestModel->find($requestId);
		$errors = $this->validate($data);

		if (!empty($errors)) {
			$statusEnum = $this->getStatus();
			require '../resources/views/asset_requests/manage.php';
			exit;
		}

		$data = $this->normalize($data);
		if (($data['status'] === 'APPROVED' || $data['status'] === 'ISSUED')
			&& $this->alreadyAssigned($assetRequest)) {

			$errors['general'] = 'This asset is already assigned.';
		} elseif ($assetRequest['status'] === 'CANCELLED' && $data['status'] !== 'CANCELLED') {

			$errors['general'] = 'This asset request is cancelled.';
		} elseif ($assetRequest['status'] === 'REJECTED' && $data['status'] !== 'REJECTED') {

			$errors['general'] = 'This asset request is rejected.';
		} elseif ($data['status'] === 'ISSUED' && $assetRequest['status'] !== 'APPROVED') {

			$errors['general'] = 'This asset is not approved yet.';
		} elseif ($data['status'] === 'RETURNED' && $assetRequest['status'] !== 'ISSUED') {

			$errors['general'] = 'This asset is not issued yet.';
		}

		if (!empty($errors['general'])) {
			$statusEnum = $this->getStatus();
			require '../resources/views/asset_requests/manage.php';
			exit;
		}

		$this->assetRequestModel->update($requestId, $data);

		if ($data['status'] === 'APPROVED') {
			(new Asset($this->conn))->updateStatus($assetRequest['asset_id'], 'ASSIGNED');
		} elseif ($data['status'] === 'RETURNED') {
			(new Asset($this->conn))->updateStatus($assetRequest['asset_id'], 'AVAILABLE');
		}

		$_SESSION['success'] = 'Asset request updated successfully';
		header("Location: index.php?route=assets/requests");
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