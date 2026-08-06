<?php

namespace App\Controllers\Asset_request;

use App\Models\Asset;
use App\Models\AssetRequest;

class SelectAssetRequestController
{
	private \PDO $conn;
	private AssetRequest $assetRequest;
	private Asset $asset;

	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->assetRequest = new AssetRequest($conn);
		$this->asset = new Asset($conn);
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

	public function show(int $id)
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to view asset request details.';
			header('Location: index.php?route=login');
			exit;
		}

		$role = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');

		if ($role === 'EMPLOYEE' || $role === 'HR') {
			require '../resources/views/errors/403.php';
			exit;
		}

		$dashboardUserRole = $role;

		$requestInfo = $this->assetRequest->find($id);

		if (empty($requestInfo)) {
			$_SESSION['login_error'] = 'Request not found.';
			header('Location: index.php?route=assets/requests');
			exit;
		}

		require '../resources/views/asset_requests/show.php';
	}
}