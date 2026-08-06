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
			header('Location: index.php?route=assets');
			exit;
		}

		require '../resources/views/asset_requests/create.php';
	}

	public function find(int $id): array
	{
		$stmt = $this->conn->prepare("SELECT * FROM asset_requests WHERE id = :id");
		$stmt->execute(['id' => $id]);
		$assetRequest = $stmt->fetch();
		if (empty($assetRequest)) {
			return [];
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
}