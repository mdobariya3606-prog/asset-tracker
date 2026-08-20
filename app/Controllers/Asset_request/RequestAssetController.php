<?php

namespace App\Controllers\Asset_request;

use App\helpers\Csrf;
use App\Models\Asset;
use App\Models\AssetRequest;
use PDO;
use Throwable;

class RequestAssetController
{
	/* =========================================================
	 * PROPERTIES
	 * ========================================================= */

	private \PDO $conn;
	private Asset $assetModel;
	private AssetRequest $assetRequestModel;

	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->assetModel = new Asset($conn);
		$this->assetRequestModel = new AssetRequest($conn);
	}

	/* =========================================================
	 * REQUEST ACTIONS
	 * ========================================================= */

	public function store(int $assetId, array $assetRequest)
	{
		if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
			view(403);
			exit;
		}

		middleware('auth');

		$this->validateRequest($assetId);

		$errors = $this->assetRequestModel->validate($assetRequest);

		if (!empty($errors)) {
			view('asset.requests.create', [
				'errors' => $errors,
			]);
			exit;
		}

		try {
			$this->conn->beginTransaction();

			// Prevent concurrent requests from modifying this asset.
			$stmt = $this->conn->prepare("
				SELECT *
				FROM assets
				WHERE id = :asset_id
				FOR UPDATE
			");

			$stmt->execute([
				'asset_id' => $assetId,
			]);

			$asset = $stmt->fetch(PDO::FETCH_ASSOC);

			if (empty($asset)) {
				$this->conn->rollBack();

				$errors['reason'] = 'Asset does not exist.';

				view('asset.requests.create', [
					'errors' => $errors,
				]);
				exit;
			}

			$stmt = $this->conn->prepare("
				INSERT INTO asset_requests
					(user_id, asset_id, asset_name, reason, due_date)
				VALUES
					(:user_id, :asset_id, :asset_name, :reason, :due_date)
			");

			$stmt->execute([
				'user_id' => $_SESSION['user_id'],
				'asset_id' => $assetId,
				'asset_name' => $asset['name'],
				'reason' => $assetRequest['reason'],
				'due_date' => $assetRequest['due_date'],
			]);

			$this->conn->commit();

			$_SESSION['success'] = 'Request sent successfully';
			route('assets');
		} catch (Throwable $e) {
			if ($this->conn->inTransaction()) {
				$this->conn->rollBack();
			}

			throw $e;
		}
	}

	/* =========================================================
	 * VALIDATION
	 * ========================================================= */

	private function validateRequest(int $assetId): void
	{
		$this->assetModel->checkAvailabity($assetId);

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
			$assetId,
		]);

		if ($stmt->rowCount() > 0) {
			$_SESSION['error'] = 'Request already sent.';
			route('assets/requests');
			exit;
		}
	}
}
