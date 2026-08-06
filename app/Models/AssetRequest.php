<?php

namespace App\Models;

class AssetRequest
{
	private \PDO $conn;

	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
	}

	public function create($asset)
	{
		$_SESSION['asset_id'] = $asset['id'];
		require '../resources/views/asset_requests/create.php';
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
}