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
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to add an asset.';
			header('Location: index.php?route=login');
			exit;
		}

		$role = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');
		if (!$this->asset->canManageAssets($role)) {
			require '../resources/views/errors/403.php';
			exit;
		}

		try {
			$errors = $this->asset->validate($data);
			if ($errors !== []) {
				$this->logError('Asset validation failed: ' . json_encode($errors));
				$assetData = $data;
				$categories = (new Category($this->conn))->all();
				$vendors = (new Vendor($this->conn))->all();
				$statusEnum = (new Asset($this->conn))->statusEnum();
				require '../resources/views/assets/create.php';
				return;
			}

			$this->asset->create($data);
			$_SESSION['success'] = 'Asset created successfully.';
			header('Location: index.php?route=assets');
			exit;
		} catch (InvalidArgumentException $e) {
			$this->logError('Asset creation error: ' . $e->getMessage());
			$errors = [$e->getMessage()];
			$assetData = $data;
			$categories = (new Category($this->conn))->all();
			$vendors = (new Vendor($this->conn))->all();
			$statusEnum = (new Asset($this->conn))->statusEnum();
			require '../resources/views/assets/create.php';
		}
	}

	private function logError(string $message): void
	{
		$logFile = __DIR__ . '/../../../logs/errors.log';
		$date = date('c');
		$line = "[{$date}] {$message}" . PHP_EOL;

		if (!is_dir(dirname($logFile))) {
			@mkdir(dirname($logFile), 0777, true);
		}

		@file_put_contents($logFile, $line, FILE_APPEND);
	}

	public function create(): void
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to add an asset.';
			header('Location: index.php?route=login');
			exit;
		}

		$role = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');
		if (!$this->asset->canManageAssets($role)) {
			require '../resources/views/errors/403.php';
			exit;
		}

		$errors = [];
		$assetData = [];
		$categories = (new Category($this->conn))->all();
		$vendors = (new Vendor($this->conn))->all();
		$statusEnum = (new Asset($this->conn))->statusEnum();
		require '../resources/views/assets/create.php';
	}

	public function edit(int $id): void
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to edit an asset.';
			header('Location: index.php?route=login');
			exit;
		}

		$role = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');
		if (!$this->asset->canManageAssets($role)) {
			require '../resources/views/errors/403.php';
			exit;
		}

		$asset = $this->asset->find($id);
		if (empty($asset)) {
			$_SESSION['general'] = 'Asset #' . $asset['id'] . ' is not found.';
			header('Location: index.php?route=assets');
			exit;
		}
		$errors = [];
		$assetData = $asset;
		$statusEnum = (new Asset($this->conn))->statusEnum();
		$categories = (new Category($this->conn))->all();
		$vendors = (new Vendor($this->conn))->all();
		require '../resources/views/assets/edit.php';
	}

	public function edit2(int $id): void
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to edit an asset.';
			header('Location: index.php?route=login');
			exit;
		}

		$role = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');
		if (!$this->asset->canManageAssets($role)) {
			require '../resources/views/errors/403.php';
			exit;
		}

		$asset = $this->asset->find($id);
		if (empty($asset)) {
			$_SESSION['login_error'] = 'Asset not found.';
			header('Location: index.php?route=assets');
			exit;
		}
		$errors = [];
		$assetData = $asset;
		$categories = (new Category($this->conn))->all();
		$vendors = (new Vendor($this->conn))->all();
		require '../resources/views/assets/edit_v1.php';
	}

	public function update(int $id, array $data): void
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to edit an asset.';
			header('Location: index.php?route=login');
			exit;
		}

		$role = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');
		if (!$this->asset->canManageAssets($role)) {
			require '../resources/views/errors/403.php';
			exit;
		}

		try {
			$errors = $this->asset->validate($data, $id);
			if (!empty($errors)) {
				$this->logError('Asset update validation failed: ' . json_encode($errors));
				$asset = $this->asset->find($id);
				$assetData = $data;
				$asset = array_merge($asset, $assetData);
				$statusEnum = (new Asset($this->conn))->statusEnum();
				$categories = (new Category($this->conn))->all();
				$vendors = (new Vendor($this->conn))->all();
				require '../resources/views/assets/edit.php';
				return;
			}

			$this->asset->update($id, $data);
			$_SESSION['success'] = 'Asset updated successfully.';
			header('Location: index.php?route=assets');
			exit;
		} catch (InvalidArgumentException $e) {
			$this->logError('Asset update error: ' . $e->getMessage());
			$errors = [$e->getMessage()];
			$asset = $this->asset->find($id);
			$assetData = $data;
			$asset = array_merge($asset, $assetData);
			$categories = (new Category($this->conn))->all();
			$vendors = (new Vendor($this->conn))->all();
			require '../resources/views/assets/edit.php';
		}
	}

	public function delete(int $id): void
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to delete an asset.';
			header('Location: index.php?route=login');
			exit;
		}

		$role = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');
		if (!$this->asset->canManageAssets($role)) {
			require '../resources/views/errors/403.php';
			exit;
		}

		$this->asset->delete($id);
		$_SESSION['success'] = 'Asset deleted successfully.';
		header('Location: index.php?route=assets');
		exit;
	}
}