<?php

namespace App\Controllers\User;

use App\Models\User;
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

		$formData = $this->showForm($id);
		if (!$formData) {
			$_SESSION['login_error'] = 'User not found.';
			route('users');;
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
			return $targetRole !== 'ADMIN';
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
		$isDeleted = (bool) $data['delete_profile_image'];

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
			$fileName = $uploadDir . $user['profile_image'];

			if (file_exists($fileName)) {
				unlink($fileName);
			}
		}

		// 3. Handle new image upload
		if (!empty($file) && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {

			// Ensure directory exists
			if (!is_dir($uploadDir)) {
				mkdir($uploadDir, 0755, true);
			}

			// Generate unique filename to prevent collisions/caching issues
			$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
			$filename = 'profile_' . $id . '.' . $extension;
			$targetPath = $uploadDir . $filename;

			if (move_uploaded_file($file['tmp_name'], $targetPath)) {
				// Store relative path or filename in data array
				$data['profile_image'] = $filename;

				// Optional: delete previous image file from disk here
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

	private function handleProfileImageUpload(int $userId, array $file): array
	{
		if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
			return [
				'success' => false,
				'errors' => ['profile_image' => 'Failed to upload profile image.'],
			];
		}

		$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
		$maxSize = 2 * 1024 * 1024;
		$mimeType = $file['type'] ?? '';
		if (!in_array($mimeType, $allowedTypes, true)) {
			return [
				'success' => false,
				'errors' => ['profile_image' => 'Invalid file type. Allowed: jpg, jpeg, png, webp.'],
			];
		}

		if (($file['size'] ?? 0) > $maxSize) {
			return [
				'success' => false,
				'errors' => ['profile_image' => 'File exceeds maximum size of 2 MB.'],
			];
		}

		$storageDir = 'storage/profile_images';
		if (!is_dir($storageDir)) {
			@mkdir($storageDir, 0777, true);
		}

		$ext = strtolower(pathinfo($file['name'] ?? 'profile.jpg', PATHINFO_EXTENSION));
		if ($ext === '') {
			$ext = 'jpg';
		}

		$filename = "profile_{$userId}.{$ext}";
		$destination = $storageDir . '/' . $filename;

		foreach (glob($storageDir . '/profile_' . $userId . '.*') as $existingFile) {
			@unlink($existingFile);
		}

		if (!move_uploaded_file($file['tmp_name'], $destination)) {
			return [
				'success' => false,
				'errors' => ['profile_image' => 'Failed to store profile image.'],
			];
		}

		$this->user->update($userId, ['profile_image' => $filename]);

		return [
			'success' => true,
			'errors' => [],
		];
	}

	public function destroy(array $getParams)
	{
		middleware('hr');

		$id = (int)($getParams['id'] ?? 0);
		$targetUser = $this->user->find($id)[0] ?? null;

		if ($targetUser === null) {
			$_SESSION['login_error'] = 'User not found.';
			route('users');;
			exit;
		}

		$viewerRole = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');
		$targetRole = strtoupper($targetUser['role'] ?? 'EMPLOYEE');
		if (!$this->canManageTarget($viewerRole, $targetRole)) {
			view(403);
			exit;
		}

		if ($this->softDelete($id)) {
			$_SESSION['success'] = 'User deleted successfully!';
		} else {
			$_SESSION['login_error'] = 'Failed to delete user.';
		}
		route('users');;
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
