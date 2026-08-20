<?php

namespace App\Controllers\Asset_request;

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\User;
use PDO;

class SelectAssetRequestController
{
	/* =========================================================
	 * PROPERTIES
	 * ========================================================= */

	private \PDO $conn;
	private AssetRequest $assetRequest;
	private Asset $asset;

	/* =========================================================
	 * CONSTRUCTOR
	 * ========================================================= */

	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->assetRequest = new AssetRequest($conn);
		$this->asset = new Asset($conn);
	}

	/* =========================================================
	 * REQUEST LIST
	 * ========================================================= */

	public function index()
	{
		middleware('auth');

		$status = trim((string) ($_GET['status'] ?? ''));
		$requests = $this->assetRequest->filtered($status);

		$statuses = [
			'PENDING',
			'APPROVED',
			'REJECTED',
			'ISSUED',
			'RETURNED',
			'CANCELLED',
		];

		view('asset.requests.select', [
			'requests' => $requests,
			'selectedStatus' => $status,
			'statuses' => $statuses,
		]);

		exit;
	}

	/* =========================================================
	 * REQUEST DETAILS
	 * ========================================================= */

	public function show(int $id)
	{
		middleware('auth');

		$role = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');

		middleware('asset');

		$dashboardUserRole = $role;
		$assetRequest = $this->assetRequest->findOrFail($id);

		$approvedBy = (new User($this->conn))
			->find($assetRequest['approved_by']);

		$rejectedBy = (new User($this->conn))
			->find($assetRequest['rejected_by']);

		$issuedBy = (new User($this->conn))
			->find($assetRequest['issued_by']);

		$assetRequest['approved_by'] =
			$approvedBy[0]['name'] ?? $assetRequest['approved_by'];

		$assetRequest['rejected_by'] =
			$rejectedBy[0]['name'] ?? $assetRequest['rejected_by'];

		$assetRequest['issued_by'] =
			$issuedBy[0]['name'] ?? $assetRequest['issued_by'];

		$canManageRequest = (
			(
				$dashboardUserRole === 'ADMIN'
				|| $dashboardUserRole === 'MANAGER'
			)
			&& $assetRequest['status'] !== 'RETURNED'
			&& $assetRequest['status'] !== 'CANCELLED'
		);

		$canCancelRequest =
			$assetRequest['user_id'] === $_SESSION['user_id'];

		view('asset.requests.show', [
			'assetRequest' => $assetRequest,
			'canManageRequest' => $canManageRequest,
			'canCancelRequest' => $canCancelRequest,
		]);
	}

	/* =========================================================
	 * CURRENT USER REQUESTS
	 * ========================================================= */

	public function myRequests(): array
	{
		$stmt = $this->conn->prepare(
			'SELECT *
			FROM asset_requests
			WHERE user_id = ?
			ORDER BY status'
		);

		$stmt->execute([
			$_SESSION['user_id'],
		]);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
