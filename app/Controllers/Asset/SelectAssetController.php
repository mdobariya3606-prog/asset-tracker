<?php

namespace App\Controllers\Asset;

use App\Models\Asset;
use App\Models\User;
use PDO;

class SelectAssetController
{
	private PDO $conn;
	private Asset $asset;

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
		$this->asset = new Asset($conn);
	}

	public function index(): void
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to view assets.';
			header('Location: index.php?route=login');
			exit;
		}

		// Render dashboard identity and access controls from the latest database
		// record instead of relying on values saved at sign-in.
		$dashboardUser = (new User($this->conn))->dashboardUser();
		$dashboardUserRole = strtoupper($dashboardUser['role'] ?? 'EMPLOYEE');

		$canManageAssets = $this->asset->canManageAssets($dashboardUserRole);
		$canRequestAsset = $this->asset->canRequestAsset($dashboardUserRole);
		$assets = $this->asset->all();

		require '../resources/views/assets/select.php';
	}

	public function show(int $id): void
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to view asset details.';
			header('Location: index.php?route=login');
			exit;
		}

		$role = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');
		$dashboardUserRole = $role;

		$asset = $this->asset->find($id);
		if (empty($asset)) {
			$_SESSION['login_error'] = 'Asset not found.';
			header('Location: index.php?route=assets');
			exit;
		}

		$canManageAssets = $this->asset->canManageAssets($role);
		$canRequestAsset = $this->asset->canRequestAsset($role);
		$isAvailable = strtoupper((string)($asset['status'] ?? '')) === 'AVAILABLE';

		require '../resources/views/assets/show.php';
	}
}