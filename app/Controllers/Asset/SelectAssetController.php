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
		middleware('auth');

		// Render dashboard identity and access controls from the latest database
		// record instead of relying on values saved at sign-in.
		$dashboardUser = (new User($this->conn))->dashboardUser();
		$dashboardUserRole = strtoupper($dashboardUser['role'] ?? 'EMPLOYEE');

		$canManageAssets = $this->asset->canManageAssets($dashboardUserRole);
		$canRequestAsset = $this->asset->canRequestAsset($dashboardUserRole);
		$assets = $this->asset->all();

		view('assets.select', [
			'assets' => $assets,
			'canManageAssets' => $canManageAssets,
			'canRequestAsset' => $canRequestAsset,
		]);
	}

	public function show(int $id): void
	{
		middleware('auth');

		$role = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');
		$dashboardUserRole = $role;

		$asset = $this->asset->find($id);

		if (empty($asset)) {
			$_SESSION['login_error'] = 'Asset not found.';
			route('assets');
			exit;
		}

		$canManageAssets = $this->asset->canManageAssets($role);
		$canRequestAsset = $this->asset->canRequestAsset($role);
		$isAvailable = strtoupper((string)($asset['status'] ?? '')) === 'AVAILABLE';
		$isAlreadyRequested = $this->isAlreadyRequested();

		view('assets.show', [
			'dashboardUserRole' => $dashboardUserRole,
			'asset' => $asset,
			'canManageAssets' => $canManageAssets,
			'canRequestAsset' => $canRequestAsset,
			'isAvailable' => $isAvailable,
			'isAlreadyRequested' => $isAlreadyRequested,
		]);
	}

	public function isAlreadyRequested()
	{
		$stmt = $this->conn->prepare('
			SELECT id 
			FROM asset_requests 
			WHERE user_id = ? 
				AND asset_id = ? 
				AND status != "CANCELLED" 
				AND status != "RETURNED" 
				AND status != "REJECTED"
		');

		$stmt->execute([$_SESSION['user_id'], $_GET['id']]);

		return $stmt->rowCount() > 0;
	}
}
