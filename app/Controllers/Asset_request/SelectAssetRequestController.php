<?php

namespace App\Controllers\Asset_request;

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\User;

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
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to view asset requests.';
			header('Location: index.php?route=login');
			exit;
		}

		if ($_SESSION['user_role'] === 'HR' || $_SESSION['user_role'] === 'EMPLOYEE') {
			view('403');
			exit;
		}

		$stmt = $this->conn->query('select * from asset_requests order by status');
		$requests = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		view('asset.requests.select', ['requests' => $requests]);
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
			view('403');
			exit;
		}

		$dashboardUserRole = $role;

		$assetRequest = $this->assetRequest->findOrFail($id);

		$approvedBy = (new User($this->conn))->find($assetRequest["approved_by"]);
		$rejected_by = (new User($this->conn))->find($assetRequest["rejected_by"]);
		$issued_by = (new User($this->conn))->find($assetRequest["issued_by"]);

		$assetRequest['approved_by'] = $approvedBy[0]['name'] ?? $assetRequest['approved_by'];
		$assetRequest['rejected_by'] = $rejected_by[0]['name'] ?? $assetRequest['rejected_by'];
		$assetRequest['issued_by'] = $issued_by[0]['name'] ?? $assetRequest['issued_by'];

		if (empty($assetRequest)) {
			$_SESSION['login_error'] = 'Request not found.';
			header('Location: index.php?route=assets/requests');
			exit;
		}

		$canManageRequest = ($assetRequest['status'] !== 'RETURNED' && $assetRequest['status'] !== 'CANCELLED');

		view('asset.requests.show', [
			'assetRequest' => $assetRequest,
			'canManageRequest' => $canManageRequest
		]);
	}
}