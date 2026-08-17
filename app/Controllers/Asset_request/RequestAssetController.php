<?php

namespace App\Controllers\Asset_request;

use App\Models\Asset;
use App\Models\AssetRequest;

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

	public function store($asset_id, $assetRequest)
	{
		$this->validateRequest();

		$asset = $this->assetModel->find($asset_id);
		if (empty($asset)) {
			$errors['reason'] = "Asset does not exists";
		}

		$errors = $this->assetRequestModel->validate($assetRequest);

		if (empty($errors)) {
			$stmt = $this->conn->prepare("INSERT INTO asset_requests (user_id, asset_id, asset_name, reason, due_date) VALUES (:user_id, :asset_id, :asset_name, :reason, :due_date)");
			$stmt->execute([
				'user_id' => $_SESSION['user_id'],
				'asset_id' => $asset_id,
				'asset_name' => $asset['name'],
				'reason' => $assetRequest['reason'],
				'due_date' => $assetRequest['due_date'],
			]);

			sendNotice(7, $_SESSION['user_id']);
			$_SESSION['success'] = "Request sent successfully";
			route('assets');
		}

		view('asset.requests.create', ['errors' => $errors]);
		exit;
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
