<?php

namespace App\Controllers\Asset_request;

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\User;
use PDO;

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
			route('login');
			exit;
		}

		if ($_SESSION['user_role'] === 'HR' || $_SESSION['user_role'] === 'EMPLOYEE') {
			$requests = $this->myRequests();
			view('asset.requests.select', ['requests' => $requests]);
			exit;
		}

		$stmt = $this->conn->query('select * from asset_requests order by status');
		$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
		view('asset.requests.select', ['requests' => $requests]);
		exit;
	}

	public function show(int $id)
	{
		require_once __DIR__ . '/../../middleware/auth.php';
		$role = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');
		
		require_once __DIR__ . '/../../middleware/asset.php';

		$dashboardUserRole = $role;

		$assetRequest = $this->assetRequest->findOrFail($id);

		$approvedBy = (new User($this->conn))->find($assetRequest["approved_by"]);
		$rejected_by = (new User($this->conn))->find($assetRequest["rejected_by"]);
		$issued_by = (new User($this->conn))->find($assetRequest["issued_by"]);

		$assetRequest['approved_by'] = $approvedBy[0]['name'] ?? $assetRequest['approved_by'];
		$assetRequest['rejected_by'] = $rejected_by[0]['name'] ?? $assetRequest['rejected_by'];
		$assetRequest['issued_by'] = $issued_by[0]['name'] ?? $assetRequest['issued_by'];

		$canManageRequest = (
			($dashboardUserRole === 'ADMIN'
			|| $dashboardUserRole === 'MANAGER')
			&& $assetRequest['status'] !== 'RETURNED' 
			&& $assetRequest['status'] !== 'CANCELLED');

		$canCancelRequest = $assetRequest['user_id'] === $_SESSION['user_id'];

		view('asset.requests.show', [
			'assetRequest' => $assetRequest,
			'canManageRequest' => $canManageRequest,
			'canCancelRequest' => $canCancelRequest,
		]);
	}

	public function myRequests(): array {
		$stmt = $this->conn->prepare('select * from asset_requests where user_id = ?');
		$stmt->execute([
			$_SESSION['user_id'],
		]);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}