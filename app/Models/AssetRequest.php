<?php

namespace App\Models;

class AssetRequest
{
	private \PDO $conn;
	private Asset $assetModel;

	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->assetModel = new Asset($conn);
	}

	public function create($id)
	{
		$asset = $this->assetModel->find($id);
		if (empty($asset) || strtoupper((string)($asset['status'] ?? '')) !== 'AVAILABLE') {
			$_SESSION['general'] = 'Asset #' . $asset['id'] . ' is not available for request.';
			route('assets');
			exit;
		}

		require '../resources/views/asset_requests/create.php';
	}

	public function findOrFail(int $id): array
	{
		$stmt = $this->conn->prepare("SELECT * FROM asset_requests WHERE id = :id");
		$stmt->execute(['id' => $id]);
		$assetRequest = $stmt->fetch();
		if (empty($assetRequest)) {
			require '../resources/views/errors/404.php';
			exit;
		}
		return $assetRequest;
	}

	public function validate($assetRequest): array
	{
		$errors = [];

		$reason = $assetRequest['reason'];

		if (empty($reason)) {
			$errors['reason'] = 'Reason is required';
		}

		return $errors;
	}

	public function update(int $id, array $request, array $assetRequest)
	{
		$stmt = $this->conn->prepare("
			UPDATE asset_requests 
			SET status = :status,
			
			    approved_at = :approved_at,
			    approved_by = :approved_by, 
			    
			    rejected_at = :rejected_at,
			    rejected_by = :rejected_by,
			    
			    rejection_reason = :rejection_reason,
			    
			    issued_at = :issued_at,
			    issued_by = :issued_by, 
			    
			    remark = :remark,
			    returned_at = :returned_at
			    
			WHERE id = :id"
		);
		$stmt->execute([
			'status' => $request['status'],

			'approved_at' => $request['approved_at'] ?? $assetRequest['approved_at'],
			'approved_by' => $request['approved_by'] ?? $assetRequest['approved_by'],

			'rejected_at' => $request['rejected_at'] ?? $assetRequest['rejected_at'],
			'rejected_by' => $request['rejected_by'] ?? $assetRequest['rejected_by'],

			'rejection_reason' => $request['rejection_reason'],

			'issued_at' => $request['issued_at'] ?? $assetRequest['issued_at'],
			'issued_by' => $request['issued_by'] ?? $assetRequest['issued_by'],

			'remark' => $request['remark'],
			'returned_at' => $request['returned_at'] ?? $assetRequest['returned_at'],

			'id' => $id,
		]);
	}

	public function pendingRequests(): int
	{
		return $this->conn->query('select count(*) from asset_requests where status = "PENDING"')->fetchColumn();
	}
}