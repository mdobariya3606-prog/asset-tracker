<?php

namespace App\Controllers\User;

use App\Models\User;
use Exception;
use PDO;

class EditUserController
{
	private PDO $conn;
	private User $user;

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
		$this->user = new User($conn);
	}

	public function edit(array $getParams)
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to edit a user.';
			route('login');
			exit;
		}

		$id = (int)($getParams['id'] ?? 0);
		$isOwnProfile = $id === (int)$_SESSION['user_id'];
		$viewerRole = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');

		// if (!$isOwnProfile) {
		// 	middleware('hr');
		// }

		$formData = $this->showForm($id);

		if (!$formData) {
			$_SESSION['login_error'] = 'User not found.';
			route('users');
			exit;
		}

		$targetUser = $formData['user'];
		$targetRole = strtoupper($targetUser['role'] ?? 'EMPLOYEE');

		if (!$this->canEditTarget($viewerRole, $targetRole, $isOwnProfile)) {
			view(403);
			exit;
		}

		$user = $targetUser;
		$departments = $formData['departments'];
		$designations = $formData['designations'];
		$errors = [];
		$old = [];

		view('users.edit', [
			'user_id' => $user['id'],
			'formData' => $formData,
			'isOwnProfile' => $isOwnProfile,
			'targetRole' => $targetRole,
			'viewerRole' => $viewerRole,
			'user' => $user,
			'departments' => $departments,
			'designations' => $designations,
			'errors' => $errors,
			'old' => $old,
		]);
	}

	/**
	 * Retrieve data for editing a user.
	 *
	 * @param int $id
	 * @return array|null
	 */
	public function showForm(int $id): ?array
	{
		$userData = $this->user->find($id);
		if (empty($userData)) {
			return null;
		}

		return [
			'user' => $userData[0],
			'departments' => $this->getDepartments(),
			'designations' => $this->getDesignations(),
		];
	}

	private function getDepartments(): array
	{
		$stmt = $this->conn->query('SELECT * FROM departments');
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	private function getDesignations(): array
	{
		$stmt = $this->conn->query('SELECT * FROM designations');
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	private function canEditTarget(string $viewerRole, string $targetRole, bool $isOwnProfile): bool
	{
		$viewerRole = strtoupper($viewerRole);
		$targetRole = strtoupper($targetRole);

		if ($this->isAdmin($viewerRole)) {
			return true;
		}

		if ($viewerRole === 'MANAGER') {
			if ($isOwnProfile) {
				return true;
			}
			return false;
		}

		if ($viewerRole === 'HR') {
			if ($isOwnProfile) {
				return true;
			}
			return !in_array($targetRole, ['ADMIN', 'MANAGER'], true);
		}

		return $isOwnProfile;
	}

	private function isAdmin(string $role): bool
	{
		return strtoupper($role) === 'ADMIN';
	}

	public function updateUser(array $getParams, array $postParams)
	{
		middleware('auth');

		$id = (int)($getParams['id'] ?? 0);
		$isOwnProfile = $id === (int)$_SESSION['user_id'];
		$viewerRole = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');

		if (!$isOwnProfile) {
			middleware('hr');
		}

		$currentUser = $this->user->find($id)[0] ?? null;

		if (!$currentUser) {
			$_SESSION['login_error'] = 'User not found.';
			route('users');
			exit;
		}

		$targetRole = strtoupper($currentUser['role'] ?? 'EMPLOYEE');
		if (!$this->canEditTarget($viewerRole, $targetRole, $isOwnProfile)) {
			view(403);
			exit;
		}

		// Apply role-based parameter restrictions
		if ($viewerRole === 'HR') {
			$postParams['role'] = $currentUser['role'];
			$postParams['designation_id'] = $currentUser['designation_id'];
		} elseif (!$this->isAdmin($viewerRole) && $isOwnProfile) {
			$postParams['department_id'] = $currentUser['department_id'];
			$postParams['designation_id'] = $currentUser['designation_id'];
			$postParams['role'] = $currentUser['role'];
		} elseif (!$this->isAdmin($viewerRole)) {
			$postParams['role'] = $currentUser['role'];
		}

		// Grab file from global $_FILES array
		$file = $_FILES['profile_image'] ?? null;

		// Execute update pipeline (handles validation, file moving, and DB update atomically)
		$result = $this->update($id, $postParams, $file);
		
		if ($result['success']) {
			if ($isOwnProfile) {
				$_SESSION['user_name'] = trim($postParams['name']);
				$_SESSION['user_email'] = strtolower(trim($postParams['email']));

				// Sync session avatar if image updated or removed
				if (array_key_exists('profile_image', $postParams)) {
					$_SESSION['user_profile_image'] = $postParams['profile_image'];
				}
			}

			$_SESSION['success'] = 'User details updated successfully!';
			route('users');
			exit;
		}

		// On failure: fetch form data and re-render edit view with errors & old input
		$formData = $this->showForm($id);
		if (!$formData) {
			$_SESSION['login_error'] = 'User not found.';
			route('users');
			exit;
		}

		view('users.edit', [
			'user_id'      => $formData['user']['id'],
			'formData'     => $formData,
			'isOwnProfile' => $isOwnProfile,
			'targetRole'   => $targetRole,
			'viewerRole'   => $viewerRole,
			'user'         => $formData['user'],
			'departments'  => $formData['departments'],
			'designations' => $formData['designations'],
			'errors'       => $result['errors'],
			'old'          => $result['old'] ?? $postParams,
		]);
	}

	/**
	 * Update a user profile.
	 *
	 * @param int $id
	 * @param array $data
	 * @return array
	 */
	public function update(int $id, array $data, ?array $file = null): array
	{
		// 1. Pass the file to validate()
		$errors = $this->user->validate($data, true, $id, $file);
		$isDeleted = !empty($data['delete_profile_image']);

		if (!empty($errors)) {
			return [
				'success' => false,
				'errors'  => $errors,
				'old'     => $data,
			];
		}

		$uploadDir = __DIR__ . '/../../../storage/profile_images/';

		// 2. Handle image deletion request (if user checked "remove profile picture")
		if ($isDeleted) {
			$data['profile_image'] = null;

			$user = (new User($this->conn))->find($id)[0];
			if (!empty($user['profile_image'])) {
				$fileName = $uploadDir . basename($user['profile_image']);

				if (file_exists($fileName)) {
					unlink($fileName);
				}
			}
		}

		// 3. Handle new image upload
		if (!empty($file) && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
			$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
			$allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
			$maxSize = 2 * 1024 * 1024;
			$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

			if (!in_array($extension, $allowedExtensions, true)) {
				return [
					'success' => false,
					'errors'  => ['profile_image' => 'Invalid file type. Allowed: jpg, jpeg, png, webp.'],
					'old'     => $data,
				];
			}

			// Verify actual file content, not just the client-supplied extension
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mimeType = finfo_file($finfo, $file['tmp_name']);
			finfo_close($finfo);

			if (!in_array($mimeType, $allowedMimes, true)) {
				return [
					'success' => false,
					'errors'  => ['profile_image' => 'Invalid file type. Allowed: jpg, jpeg, png, webp.'],
					'old'     => $data,
				];
			}

			if ($file['size'] > $maxSize) {
				return [
					'success' => false,
					'errors'  => ['profile_image' => 'File exceeds maximum size of 2 MB.'],
					'old'     => $data,
				];
			}

			if (!is_dir($uploadDir)) {
				mkdir($uploadDir, 0755, true);
			}

			$filename = 'profile_' . $id . '.' . $extension;
			$targetPath = $uploadDir . $filename;

			if (move_uploaded_file($file['tmp_name'], $targetPath)) {
				$data['profile_image'] = $filename;
			} else {
				return [
					'success' => false,
					'errors'  => ['profile_image' => 'Failed to save the uploaded file.'],
					'old'     => $data,
				];
			}
		}

		// 4. Perform database update
		$success = $this->user->update($id, $data);

		return [
			'success' => $success,
			'errors'  => $success ? [] : ['general' => 'Failed to update user in the database.'],
		];
	}

	public function destroy(array $getParams, bool $deletePerm = false)
	{
		middleware('hr');

		$id = (int)($getParams['id'] ?? 0);
		$targetUser = $this->user->find($id)[0] ?? null;

		if ($targetUser === null) {
			$_SESSION['login_error'] = 'User not found.';
			route('users');
			exit;
		}

		$viewerRole = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');
		$targetRole = strtoupper($targetUser['role'] ?? 'EMPLOYEE');
		if (!$this->canManageTarget($viewerRole, $targetRole)) {
			view(403);
			exit;
		}

		if ($this->user->hasIssuedAssets($id)) {
			$_SESSION['login_error'] = 'Cannot delete user: This user currently has assigned/issued assets. Please return all assets before deleting.';
			route('users');
			exit;
		}

		if ($deletePerm) {
			$this->conn->beginTransaction();
			try {
				$this->user->deletePermanantly($id);
				$path = __DIR__ . '/../../../storage/profile_images/' . $targetUser['profile_image'];

				if (file_exists($path)) {
					unlink($path);
				}

				$this->conn->commit();
			} catch (Exception $e) {
				$this->conn->rollBack();
			}
		} else {
			if ($this->softDelete($id)) {
				$_SESSION['success'] = 'User deleted successfully!';
			} else {
				$_SESSION['login_error'] = 'Failed to delete user.';
			}
		}

		route('users');
		exit;
	}

	private function canManageTarget(string $viewerRole, string $targetRole): bool
	{
		$viewerRole = strtoupper($viewerRole);
		$targetRole = strtoupper($targetRole);

		if ($this->isAdmin($viewerRole)) {
			return true;
		}

		if ($viewerRole === 'MANAGER') {
			return $targetRole !== 'ADMIN';
		}

		if ($viewerRole === 'HR') {
			return $targetRole === 'EMPLOYEE';
		}

		return false;
	}

	/**
	 * Delete a user by ID.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function softDelete(int $id): bool
	{
		return $this->user->softDelete($id);
	}
}
