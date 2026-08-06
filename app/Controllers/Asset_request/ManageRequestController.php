<?php

namespace App\Controllers\Asset_request;

use App\Models\AssetRequest;

class ManageRequestController
{
	private \PDO $conn;
	private AssetRequest $assetRequestModel;

	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->assetRequestModel = new AssetRequest($conn);
	}

	public function show(int $id)
	{
		$role = $_SESSION['user_role'] ?? 'EMPLOYEE';

		if ($role === 'EMPLOYEE' || $role === 'HR') {
			require '../resources/view/errors/403.php';
			exit;
		}

		$assetRequest = $this->assetRequestModel->find((int)$id);

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

	public function update(int $id, array $request)
	{
		$assetRequest = $this->assetRequestModel->find($id);
		$errors = $this->validate($request);

		if (!empty($errors)) {
			$statusEnum = $this->getStatus();
			require '../resources/views/asset_requests/manage.php';
			exit;
		}

		$request = $this->normalize($request);
		$this->assetRequestModel->update($id, $request);

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
}