<?php

namespace App\Controllers\Asset;

use App\helpers\Csrf;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Vendor;
use InvalidArgumentException;
use PDO;

class CreateAssetController
{
	private PDO $conn;
	private Asset $asset;

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
		$this->asset = new Asset($conn);
	}

	public function store(array $data): void
	{
		if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
			view(403);
			exit;
		}

		middleware('auth');
		middleware('manager');

		// Step 1: Validate input fields first before creating
		$errors = $this->asset->validate($data);
		if (!empty($errors)) {
			$assetData = $data;
			$categories = (new Category($this->conn))->all();
			$vendors = (new Vendor($this->conn))->all();
			$statusEnum = $this->asset->statusEnum();

			view('assets.create', [
				'errors' => $errors,
				'assetData' => $assetData,
				'categories' => $categories,
				'vendors' => $vendors,
				'statusEnum' => $statusEnum,
			]);
			exit;
		}

		// Step 2: Create asset in database to get ID
		try {
			$assetId = $this->asset->create($data);
		} catch (InvalidArgumentException $e) {
			$errors = ['general' => $e->getMessage()];
			$assetData = $data;
			$categories = (new Category($this->conn))->all();
			$vendors = (new Vendor($this->conn))->all();
			$statusEnum = $this->asset->statusEnum();

			view('assets.create', [
				'errors' => $errors,
				'assetData' => $assetData,
				'categories' => $categories,
				'vendors' => $vendors,
				'statusEnum' => $statusEnum,
			]);
			exit;
		}

		$_SESSION['success'] = 'Asset created successfully.';
		route('assets');
		exit;
	}

	public function create(): void
	{
		middleware('auth');
		middleware('manager');

		$errors = [];
		$assetData = [];
		$categories = (new Category($this->conn))->all();
		$vendors = (new Vendor($this->conn))->all();
		$statusEnum = $this->asset->statusEnum();

		view('assets.create', [
			'errors' => $errors,
			'assetData' => $assetData,
			'categories' => $categories,
			'vendors' => $vendors,
			'statusEnum' => $statusEnum,
		]);
		exit;
	}

	public function edit(int $id): void
	{
		middleware('auth');
		middleware('manager');

		$asset = $this->asset->find($id);
		if (empty($asset)) {
			$_SESSION['general'] = 'Asset #' . $id . ' is not found.';
			route('assets');
			exit;
		}

		$errors = [];
		$assetData = $asset;
		$statusEnum = $this->asset->statusEnum();
		$categories = (new Category($this->conn))->all();
		$vendors = (new Vendor($this->conn))->all();

		view('assets.edit', [
			'errors' => $errors,
			'assetData' => $assetData,
			'statusEnum' => $statusEnum,
			'categories' => $categories,
			'vendors' => $vendors,
		]);
		exit;
	}

	public function update(int $id, array $inputData): void
	{
		if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
			view(403);
			exit;
		}

		middleware('auth');
		middleware('manager');

		$existingAsset = $this->asset->find($id);
		if (empty($existingAsset)) {
			$_SESSION['general'] = 'Asset #' . $id . ' is not found.';
			route('assets');
			exit;
		}

		// Step 1: Validate input data
		$errors = $this->asset->validate($inputData, $id);
		if (!empty($errors)) {
			$assetData = $inputData;
			$asset = array_merge($existingAsset, $assetData);
			$statusEnum = $this->asset->statusEnum();
			$categories = (new Category($this->conn))->all();
			$vendors = (new Vendor($this->conn))->all();

			view('assets.edit', [
				'errors' => $errors,
				'old' => $inputData,
				'assetData' => $assetData,
				'asset' => $asset,
				'statusEnum' => $statusEnum,
				'categories' => $categories,
				'vendors' => $vendors,
			]);
			return;
		}

		// Step 2: Update record in database
		try {
			$this->asset->update($id, $inputData);
		} catch (InvalidArgumentException $e) {
			$errors = ['general' => $e->getMessage()];
			$assetData = $inputData;
			$asset = array_merge($existingAsset, $assetData);
			$statusEnum = $this->asset->statusEnum();
			$categories = (new Category($this->conn))->all();
			$vendors = (new Vendor($this->conn))->all();

			view('assets.edit', [
				'errors' => $errors,
				'old' => $inputData,
				'assetData' => $assetData,
				'asset' => $asset,
				'statusEnum' => $statusEnum,
				'categories' => $categories,
				'vendors' => $vendors,
			]);
			return;
		}

		$_SESSION['success'] = 'Asset updated successfully.';
		route('assets');
		exit;
	}

	public function delete(int $id): void
	{
		middleware('auth');
		middleware('manager');

		if ($this->asset->isIssued($id)) {
			$_SESSION['general'] = 'Cannot delete asset #' . $id . ': This asset is currently issued to a user. Please return the asset before deleting.';
			route('assets');
			exit;
		}

		$storageDir = 'storage/asset_images';
		foreach (glob($storageDir . '/asset_' . $id . '.*') as $existingFile) {
			@unlink($existingFile);
		}

		$this->asset->delete($id);
		$_SESSION['success'] = 'Asset deleted successfully.';
		route('assets');
		exit;
	}
}
