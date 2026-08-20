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
	 * CREATE ASSET
	 * ========================================================= */

	public function create(): void
	{
		middleware('auth');
		middleware('manager');

		$this->renderCreateForm([], []);
		exit;
	}

	/* =========================================================
	 * STORE ASSET
	 * ========================================================= */

	public function store(array $data): void
	{
		// Validate CSRF token before processing the request.
		if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
			view(403);
			exit;
		}

		middleware('auth');
		middleware('manager');

		// Validate submitted asset data.
		$errors = $this->asset->validate($data);

		if (!empty($errors)) {
			$this->renderCreateForm($errors, $data);
			exit;
		}

		// Create the asset after successful validation.
		try {
			$this->asset->create($data);
		} catch (InvalidArgumentException $e) {
			$this->renderCreateForm(
				['general' => $e->getMessage()],
				$data
			);
			exit;
		}

		$_SESSION['success'] = 'Asset created successfully.';
		route('assets');
		exit;
	}

	/* =========================================================
	 * EDIT ASSET
	 * ========================================================= */

	public function edit(int $id): void
	{
		middleware('auth');
		middleware('manager');

		// Fetch the asset that will be edited.
		$asset = $this->asset->find($id);

		if (empty($asset)) {
			$_SESSION['general'] = 'Asset #' . $id . ' is not found.';
			route('assets');
			exit;
		}

		$this->renderEditForm([], $asset, $asset);
		exit;
	}

	/* =========================================================
	 * UPDATE ASSET
	 * ========================================================= */

	public function update(int $id, array $inputData): void
	{
		// Validate CSRF token before processing the request.
		if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
			view(403);
			exit;
		}

		middleware('auth');
		middleware('manager');

		// Fetch the existing asset before updating it.
		$existingAsset = $this->asset->find($id);

		if (empty($existingAsset)) {
			view(404);
			exit;
		}

		// Validate submitted asset data.
		$errors = $this->asset->validate($inputData, $id);

		if (!empty($errors)) {
			// Keep existing values while replacing submitted fields.
			$merged = array_merge($existingAsset, $inputData);

			$this->renderEditForm(
				$errors,
				$inputData,
				$merged
			);

			return;
		}

		// Update the asset after successful validation.
		try {
			$this->asset->update($id, $inputData);
		} catch (InvalidArgumentException $e) {
			$merged = array_merge($existingAsset, $inputData);

			$this->renderEditForm(
				['general' => $e->getMessage()],
				$inputData,
				$merged
			);

			return;
		}

		$_SESSION['success'] = 'Asset updated successfully.';
		route('assets');
		exit;
	}

	/* =========================================================
	 * DELETE ASSET
	 * ========================================================= */

	public function delete(): void
	{
		// Validate CSRF token before processing the request.
		if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
			view(403);
			exit;
		}

		middleware('auth');
		middleware('manager');

		if (empty($_POST['id'])) {
			view(404);
			exit;
		}

		$id = (int) $_POST['id'];

		// An issued asset must be returned before it can be deleted.
		if ($this->asset->isIssued($id)) {
			$_SESSION['general'] =
				'Cannot delete asset #' . $id .
				': This asset is currently issued to a user. ' .
				'Please return the asset before deleting.';

			route('assets');
			exit;
		}

		$this->asset->delete($id);

		$_SESSION['success'] = 'Asset deleted successfully.';
		route('assets');
		exit;
	}

	/* =========================================================
	 * CREATE FORM
	 * ========================================================= */

	private function renderCreateForm(
		array $errors,
		array $assetData
	): void {
		view('assets.create', [
			'errors' => $errors,
			'assetData' => $assetData,
			'categories' => (new Category($this->conn))->all(),
			'vendors' => (new Vendor($this->conn))->all(),
			'statusEnum' => $this->asset->statusEnum(),
		]);
	}

	/* =========================================================
	 * EDIT FORM
	 * ========================================================= */

	private function renderEditForm(
		array $errors,
		array $inputData,
		array $asset
	): void {
		view('assets.edit', [
			'errors' => $errors,
			'old' => $inputData,
			'assetData' => $inputData,
			'asset' => $asset,
			'statusEnum' => $this->asset->statusEnum(),
			'categories' => (new Category($this->conn))->all(),
			'vendors' => (new Vendor($this->conn))->all(),
		]);
	}
}
