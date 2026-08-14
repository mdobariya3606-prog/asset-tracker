<?php

namespace App\Controllers\User;

use App\Models\AuditLog;
use App\Models\User;
use Exception;
use PDO;

class CreateUserController
{
	private PDO $conn;
	private User $user;

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
		$this->user = new User($conn);
	}

	public function store(array $postData): void
	{
		try {
			middleware('auth');
			middleware('admin');

			// Separate file data from post data
			$file = $_FILES['profile_image'] ?? null;

			$result = $this->register($postData);

			if ($result['success']) {

				$userId = $result['user_id'];

				// Handle profile image upload if provided
				if ($file && $file['error'] === UPLOAD_ERR_OK) {
					$allowed = ['image/jpeg', 'image/png', 'image/webp'];
					$maxSize = 2 * 1024 * 1024; // 2 MB
					if (!in_array($file['type'], $allowed, true)) {
						$result['success'] = false;
						$result['errors']['profile_image'] = 'Invalid file type. Allowed: jpg, jpeg, png, webp.';
					} elseif ($file['size'] > $maxSize) {
						$result['success'] = false;
						$result['errors']['profile_image'] = 'File exceeds maximum size of 2 MB.';
					} else {
						$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
						$filename = "profile_{$userId}." . strtolower($ext);
						$dest = __DIR__ . '/../../../storage/profile_images/' . $filename;
						if (!move_uploaded_file($file['tmp_name'], $dest)) {
							$result['success'] = false;
							$result['errors']['profile_image'] = 'Failed to move uploaded file.';
						} else {
							// Update user with image filename
							$this->user->update($userId, ['profile_image' => $filename]);
						}
					}
				}
				if ($result['success']) {
					$_SESSION['success'] = 'User registered successfully!';

					(new AuditLog($this->conn))->log('USER_CREATION', null, $userId);
					route('users');
					exit;
				}
			}

			// On failure, re‑display form with errors
			$errors = $result['errors'];
			$old = $result['old'];

			// Log errors for debugging
			logError('User registration errors: ' . json_encode($errors));
			$formData = $this->showForm();
			$departments = $formData['departments'];
			$designations = $formData['designations'];
			$roleOptions = $formData['role_options'];

			view('users.register', [
				'departments' => $departments,
				'designations' => $designations,
				'roleOptions' => $roleOptions,
				'old' => $old,
				'errors' => $errors,
			]);
		} catch (Exception $e) {
			logError($e);
			$_SESSION['login_error'] = 'An unexpected error occurred.';
			route('users/create');
			exit;
		}
	}

	public function register(array $data): array
	{
		$errors = $this->user->validate($data);
		$roleErrors = $this->validateAssignedRole($data);
		$errors = array_merge($errors, $roleErrors);
		if (!empty($errors)) {
			return [
				'success' => false,
				'errors' => $errors,
				'old' => $data,
			];
		}
		// Create user without profile image first to obtain id
		$userId = $this->user->create($data);
		return [
			'success' => true,
			'errors' => [],
			'user_id' => $userId,
		];
	}

	private function validateAssignedRole(array $data): array
	{
		$allowedRoles = array_column($this->getRoleOptions(), 'value');
		$selectedRole = strtoupper(trim($data['role'] ?? 'EMPLOYEE'));

		if (empty($allowedRoles)) {
			return [];
		}

		if (!in_array($selectedRole, $allowedRoles, true)) {
			return ['role' => 'You are not allowed to assign this role.'];
		}

		return [];
	}

	private function getRoleOptions(): array
	{
		$viewerRole = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');

		if ($viewerRole === 'ADMIN') {
			return [
				['value' => 'EMPLOYEE', 'label' => 'Employee'],
				['value' => 'MANAGER', 'label' => 'Manager'],
				['value' => 'HR', 'label' => 'HR'],
				['value' => 'ADMIN', 'label' => 'Admin'],
			];
		}

		if ($viewerRole === 'MANAGER') {
			return [
				['value' => 'EMPLOYEE', 'label' => 'Employee'],
				['value' => 'MANAGER', 'label' => 'Manager'],
				['value' => 'HR', 'label' => 'HR'],
			];
		}

		if ($viewerRole === 'HR') {
			return [
				['value' => 'EMPLOYEE', 'label' => 'Employee'],
			];
		}

		return [];
	}

	public function create(): void
	{
		middleware('auth');
		middleware('admin');

		$formData = $this->showForm();

		view('users.register', [
			'formData' => $formData,
			'departments' => $formData['departments'],
			'designations' => $formData['designations'],
			'roleOptions' => $formData['role_options'],
			'errors' => [],
			'old' => [],
			'success' => $_SESSION['success'] ?? null,
		]);
		unset($_SESSION['success']);
		exit;
	}

	public function showForm(): array
	{
		return [
			'departments' => $this->getDepartments(),
			'designations' => $this->getDesignations(),
			'role_options' => $this->getRoleOptions(),
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
}
