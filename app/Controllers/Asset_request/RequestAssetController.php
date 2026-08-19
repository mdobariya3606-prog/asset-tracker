<?php

namespace App\Controllers\Asset_request;

use App\helpers\Csrf;
use App\Models\Asset;
use App\Models\AssetRequest;
use PDO;
use Throwable;

class RequestAssetController
{
	private \PDO $conn;
	private AssetRequest $assetRequestModel;
	private Asset $assetModel;

	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->assetModel = new Asset($conn);
		$this->assetRequestModel = new AssetRequest($conn);
	}

	public function store(int $asset_id, array $assetRequest)
	{
		if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
			view(403);
			exit;
		}

		$this->validateRequest();

		$errors = $this->assetRequestModel->validate($assetRequest);

		if (!empty($errors)) {
			view('asset.requests.create', ['errors' => $errors]);
			exit;
		}

		try {
			$this->conn->beginTransaction();

			// Lock the asset row until this transaction finishes
			$stmt = $this->conn->prepare("
				SELECT *
				FROM assets
				WHERE id = :asset_id
				FOR UPDATE
			");

			$stmt->execute([
				'asset_id' => $asset_id
			]);

			$asset = $stmt->fetch(PDO::FETCH_ASSOC);

			if (empty($asset)) {
				$this->conn->rollBack();

				$errors['reason'] = "Asset does not exist.";
				view('asset.requests.create', ['errors' => $errors]);
				exit;
			}

			$stmt = $this->conn->prepare("
            INSERT INTO asset_requests
                (user_id, asset_id, asset_name, reason, due_date)
            VALUES
                (:user_id, :asset_id, :asset_name, :reason, :due_date)
        ");

			$stmt->execute([
				'user_id'   => $_SESSION['user_id'],
				'asset_id'  => $asset_id,
				'asset_name' => $asset['name'],
				'reason'    => $assetRequest['reason'],
				'due_date'  => $assetRequest['due_date'],
			]);

			$this->conn->commit();

			$_SESSION['success'] = "Request sent successfully";
			route('assets');
		} catch (Throwable $e) {
			if ($this->conn->inTransaction()) {
				$this->conn->rollBack();
			}

			throw $e;
		}
	}

	private function validateRequest()
	{
		$assetModel = (new Asset($this->conn));
		$assetId = $_GET['id'] ?? null;

		if (!$assetModel->isAvailable($assetId)) {
			$_SESSION['general'] = 'Asset #' . $assetId . ' is not available for request.';
			route('assets');
			exit;
		}

		$stmt = $this->conn->prepare('
		SELECT id FROM asset_requests 
		WHERE user_id = ? 
			AND asset_id = ? 
			AND status != "CANCELLED" 
			AND status != "RETURNED"
			AND status != "REJECTED"');

		$stmt->execute([$_SESSION['user_id'], $assetId]);

		if ($stmt->rowCount() > 0) {
			$_SESSION['success'] = 'Request already sent.';
			route('assets/requests');
			exit;
		}
	}
}
