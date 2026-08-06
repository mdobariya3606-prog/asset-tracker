<?php

namespace App\Controllers\Asset_request;

use App\Models\AssetRequest;

class SelectAssetRequestController
{
	private \PDO $conn;
	private AssetRequest $assetRequest;

	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->assetRequest = new AssetRequest($conn);
	}

	public function index()
	{
		if ($_SESSION['user_role'] === 'HR' || $_SESSION['user_role'] === 'EMPLOYEE') {
			require '../resources/views/errors/403.php';
			exit;
		}
		$stmt = $this->conn->query('select * from asset_requests order by status');
		$requests = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		require '../resources/views/asset_requests/select.php';
		exit;
	}
}