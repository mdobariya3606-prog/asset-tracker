<?php

namespace App\Controllers\User;

use App\Models\User;
use PDO;

class ResetPasswordController
{
	private PDO $conn;
	private User $user;

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
		$this->user = new User($conn);
	}

	public function edit(array $getParams): void
	{
		$id = (int)($getParams['id'] ?? 0);
		$user = $this->user->find($id)[0] ?? null;
		if ($user === null) {
			$_SESSION['login_error'] = 'User not found.';
			route('users');;
			exit;
		}

		$this->authorizePrivileged($user['role'] ?? 'EMPLOYEE');

		$errors = [];
		$old = [];
		require '../resources/views/users/reset-password.php';
	}

	private function authorizePrivileged(string $targetRole): void
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to reset a password.';
			route('login');
			exit;
		}

		$viewerRole = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');
		$targetRole = strtoupper($targetRole);

		if ($viewerRole === 'ADMIN') {
			return;
		}

		if ($viewerRole === 'MANAGER') {
			if ($targetRole === 'ADMIN') {
				view('403');
				exit;
			}
			return;
		}

		if ($viewerRole === 'HR') {
			if (in_array($targetRole, ['ADMIN', 'MANAGER'], true)) {
				view('403');
				exit;
			}
			return;
		}

		view('403');
		exit;
	}

	public function store(array $getParams, array $postParams): void
	{
		$id = (int)($getParams['id'] ?? 0);
		$user = $this->user->find($id)[0] ?? null;
		if ($user === null) {
			$_SESSION['login_error'] = 'User not found.';
			route('users');;
			exit;
		}

		$this->authorizePrivileged($user['role'] ?? 'EMPLOYEE');

		$password = (string)($postParams['password'] ?? '');
		$confirmation = (string)($postParams['password_confirmation'] ?? '');
		$errors = [];
		if (strlen($password) < 6) {
			$errors['password'] = 'Password must be 6 or more characters.';
		}
		if ($password !== $confirmation) {
			$errors['password_confirmation'] = 'Passwords do not match.';
		}

		if (!empty($errors)) {
			$old = $postParams;
			require '../resources/views/users/reset-password.php';
			return;
		}

		if (!$this->user->resetPassword($id, $password)) {
			$errors['general'] = 'Failed to reset the password.';
			$old = [];
			require '../resources/views/users/reset-password.php';
			return;
		}

		$_SESSION['success'] = 'Password reset successfully for ' . $user['name'] . '.';
		route('users');;
		exit;
	}
}