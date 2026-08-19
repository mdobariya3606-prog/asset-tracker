<?php

namespace App\Controllers\Asset;

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

		// Step 3: Handle image upload using generated asset ID
		$file = $_FILES['image'] ?? null;
		if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
			$imageResult = $this->handleAssetImageUpload($assetId, $file);
			if (!$imageResult['success']) {
				$_SESSION['general'] = 'Asset created, but image failed to upload: ' . implode(', ', $imageResult['errors']);
				route('assets');
				exit;
			}
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

	/**
	 * Asset image uploader - matches User profile image implementation
	 */
	private function handleAssetImageUpload(int $assetId, array $file): array
	{
		if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
			return [
				'success' => false,
				'errors' => ['image' => 'Failed to upload asset image.'],
			];
		}

		$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
		$maxSize = 5 * 1024 * 1024; // 5 MB

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimeType = finfo_file($finfo, $file['tmp_name']);
		finfo_close($finfo);

		if (!in_array($mimeType, $allowedTypes, true)) {
			return [
				'success' => false,
				'errors' => ['image' => 'Invalid file type. Allowed: JPG, JPEG, PNG, WEBP.'],
			];
		}

		if (($file['size'] ?? 0) > $maxSize) {
			return [
				'success' => false,
				'errors' => ['image' => 'File exceeds maximum size of 5 MB.'],
			];
		}

		$storageDir = 'storage/asset_images';
		if (!is_dir($storageDir)) {
			@mkdir($storageDir, 0775, true);
		}

		$ext = strtolower(pathinfo($file['name'] ?? 'asset.jpg', PATHINFO_EXTENSION));
		if ($ext === '') {
			$ext = 'jpg';
		}

		$filename = "asset_{$assetId}.{$ext}";
		$destination = $storageDir . '/' . $filename;
		$relativePath = 'storage/asset_images/' . $filename;

		// Delete any previous asset image with different extension
		foreach (glob($storageDir . '/asset_' . $assetId . '.*') as $existingFile) {
			@unlink($existingFile);
		}

		if (!move_uploaded_file($file['tmp_name'], $destination)) {
			return [
				'success' => false,
				'errors' => ['image' => 'Failed to store asset image.'],
			];
		}

		// Direct SQL update to bypass full model validation on image-only update
		$stmt = $this->conn->prepare('UPDATE assets SET image = :image WHERE id = :id');
		$stmt->execute(['image' => $relativePath, 'id' => $assetId]);

		return [
			'success' => true,
			'errors' => [],
		];
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

		// Step 3: Handle image upload
		$file = $_FILES['image'] ?? null;
		if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
			$imageResult = $this->handleAssetImageUpload($id, $file);
			if (!$imageResult['success']) {
				$errors = $imageResult['errors'];
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
