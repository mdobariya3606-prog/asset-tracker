<?php

namespace App\Controllers\Asset;

use App\Models\Asset;
use App\Models\Category;
use App\Models\User;
use PDO;

class SelectAssetController
{
	/* =========================================================
	 * PROPERTIES
	 * ========================================================= */

	private PDO $conn;
	private Asset $asset;

	/* =========================================================
	 * CONSTRUCTOR
	 * ========================================================= */

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
		$this->asset = new Asset($conn);
	}

	/* =========================================================
	 * ASSET LIST
	 * ========================================================= */

	public function index(): void
	{
		middleware('auth');

		// Get the latest user information from the database
		// instead of relying on role data stored at login.
		$dashboardUser = (new User($this->conn))->dashboardUser();
		$dashboardUserRole = strtoupper(
			$dashboardUser['role'] ?? 'EMPLOYEE'
		);

		// Determine which asset actions are available to the user.
		$canManageAssets = $this->asset->canManageAssets($dashboardUserRole);
		$canRequestAsset = $this->asset->canRequestAsset($dashboardUserRole);

		// Get asset filters from the request.
		$categoryId = !empty($_GET['category_id'])
			? (int) $_GET['category_id']
			: null;

		$status = !empty($_GET['status'])
			? trim((string) $_GET['status'])
			: null;

		$search = isset($_GET['search'])
			? trim((string) $_GET['search'])
			: '';

		// Load filter options and filtered assets.
		$categories = (new Category($this->conn))->all();
		$statuses = $this->asset->getStatus();

		$assets = $this->asset->all(
			$categoryId,
			$status,
			$search !== '' ? $search : null
		);

		// Render the asset selection page.
		view('assets.select', [
			'assets' => $assets,
			'categories' => $categories,
			'statuses' => $statuses,
			'selectedCategoryId' => $categoryId,
			'selectedStatus' => $status,
			'search' => $search,
			'canManageAssets' => $canManageAssets,
			'canRequestAsset' => $canRequestAsset,
		]);
	}

	/* =========================================================
	 * ASSET DETAILS
	 * ========================================================= */

	public function show(int $id): void
	{
		middleware('auth');

		// Get the user's role for access control.
		$role = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');
		$dashboardUserRole = $role;

		// Fetch the requested asset.
		$asset = $this->asset->find($id);

		if (empty($asset)) {
			$_SESSION['login_error'] = 'Asset not found.';
			route('assets');
			exit;
		}

		// Determine available actions for the current user.
		$canManageAssets = $this->asset->canManageAssets($role);
		$canRequestAsset = $this->asset->canRequestAsset($role);

		// Check whether the asset is currently available.
		$isAvailable =
			strtoupper((string) ($asset['status'] ?? '')) === 'AVAILABLE';

		// Check whether the current user already has an active request.
		$isAlreadyRequested = $this->isAlreadyRequested();

		// Render the asset details page.
		view('assets.show', [
			'dashboardUserRole' => $dashboardUserRole,
			'asset' => $asset,
			'canManageAssets' => $canManageAssets,
			'canRequestAsset' => $canRequestAsset,
			'isAvailable' => $isAvailable,
			'isAlreadyRequested' => $isAlreadyRequested,
		]);
	}

	/* =========================================================
	 * ASSET HISTORY
	 * ========================================================= */

	public function history(int $id): void
	{
		middleware('auth');
		middleware('manager');

		// Fetch the asset before loading its assignment history.
		$asset = $this->asset->find($id);

		if (empty($asset)) {
			$_SESSION['login_error'] = 'Asset not found.';
			route('assets');
			exit;
		}

		// Retrieve the assignment history for the asset.
		$history = $this->asset->getAssignmentHistory($id);

		// Render the asset history page.
		view('assets.history', [
			'asset' => $asset,
			'history' => $history,
		]);
	}

	/* =========================================================
	 * REQUEST STATUS CHECK
	 * ========================================================= */

	public function isAlreadyRequested()
	{
		// Check whether the current user has an active request
		// for the selected asset.
		$stmt = $this->conn->prepare('
			SELECT id
			FROM asset_requests
			WHERE user_id = ?
				AND asset_id = ?
				AND status != "CANCELLED"
				AND status != "RETURNED"
				AND status != "REJECTED"
		');

		$stmt->execute([
			$_SESSION['user_id'],
			$_GET['id'],
		]);

		return $stmt->rowCount() > 0;
	}
}
