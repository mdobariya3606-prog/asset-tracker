<?php

namespace App\Controllers\Asset_request;

use App\Models\Asset;
use App\Models\AssetRequest;

class RequestAssetController
{
	private \PDO $conn;
	private AssetRequest $assetRequestModel;

	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->assetRequestModel = new AssetRequest($conn);
	}

	public function store($asset_id, $assetRequest)
	{
		$asset = (new Asset($this->conn))->find($asset_id);
		if (empty($asset)) {
			$errors['reason'] = "Asset does not exists";
		}

		$errors = $this->assetRequestModel->validate($assetRequest);

		if (empty($errors)) {
			$stmt = $this->conn->prepare("INSERT INTO asset_requests (user_id, asset_id, asset_name, reason) 
												VALUES (:user_id, :asset_id, :asset_name, :reason)");
			$stmt->execute([
				'user_id' => $_SESSION['user_id'],
				'asset_id' => $asset_id,
				'asset_name' => $asset['name'],
				'reason' => $assetRequest['reason']
			]);

			$_SESSION['success'] = "Request sent successfully";
			route('assets');
		}

		view('asset.requests.create', ['errors' => $errors]);
		exit;
	}

}