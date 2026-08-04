<?php

namespace App\Controllers\User;

use App\Models\User;
use PDO;

class CreateUserController
{
	private PDO $conn;
	private User $user;

	public function __construct(PDO $conn) {
		$this->conn = $conn;
		$this->user = new User($conn);
	}

	public function showForm(): array {
		return [
			'departments' => $this->getDepartments(),
			'designations' => $this->getDesignations(),
		];
	}

	public function register(array $data): array {
		$errors = $this->user->validate($data);

		if (!empty($errors)) {
			return [
				'success' => false,
				'errors' => $errors,
				'old' => $data,
			];
		}

		$userId = $this->user->create($data);

		return [
			'success' => true,
			'errors' => [],
			'user_id' => $userId,
		];
	}

	public function create() {
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to add users.';
			header('Location: index.php?route=login');
			exit;
		}
		if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'EMPLOYEE') {
			require '../resources/views/errors/403.php';
			exit;
		}

		$formData = $this->showForm();
		$departments = $formData['departments'];
		$designations = $formData['designations'];
		$errors = [];
		$old = [];
		$success = $_SESSION['success'] ?? null;
		unset($_SESSION['success']);

		require '../resources/views/users/register.php';
	}

	public function store(array $postData) {
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to add users.';
			header('Location: index.php?route=login');
			exit;
		}
		if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'EMPLOYEE') {
			require '../resources/views/errors/403.php';
			exit;
		}

		$result = $this->register($postData);

		if ($result['success']) {
			$_SESSION['success'] = 'User registered successfully!';
			header('Location: index.php?route=users');
			exit;
		}

		$errors = $result['errors'];
		$old = $result['old'];
		$formData = $this->showForm();
		$departments = $formData['departments'];
		$designations = $formData['designations'];

		require '../resources/views/users/register.php';
	}

	private function getDepartments(): array {
		$stmt = $this->conn->query('SELECT * FROM departments');
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	private function getDesignations(): array {
		$stmt = $this->conn->query('SELECT * FROM designations');
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}