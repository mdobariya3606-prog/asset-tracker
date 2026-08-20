<?php

namespace App\Controllers\User;

use App\helpers\Csrf;
use App\Models\AuditLog;
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
			route('users');
			exit;
		}

		$this->authorizePrivileged($user['role'] ?? 'EMPLOYEE');

		view('users.reset-password', [
			'user' => $user,
			'errors' => [],
			'old' => [],
		]);
		exit;
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
				view(403);
				exit;
			}

			return;
		}

		if ($viewerRole === 'HR') {
			if (in_array($targetRole, ['ADMIN', 'MANAGER'], true)) {
				view(403);
				exit;
			}

			return;
		}

		view(403);
		exit;
	}

	public function store(array $getParams, array $postParams): void
	{
		if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
			view(403);
			exit;
		}

		$id = (int)($getParams['id'] ?? 0);
		$user = $this->user->find($id)[0] ?? null;

		if ($user === null) {
			$_SESSION['login_error'] = 'User not found.';
			route('users');
			exit;
		}

		$this->authorizePrivileged($user['role'] ?? 'EMPLOYEE');

		$password = (string)($postParams['password'] ?? '');
		$confirmation = (string)($postParams['password_confirmation'] ?? '');
		$errors = [];

		if (empty($password)) {
			$errors['password'] = 'Password is required.';
		} elseif (strlen($password) < 8 || strlen($password) > 30) {
			$errors['password'] = 'Password must be 8–30 characters long.';
		} elseif (!preg_match('/[A-Z]/', $password)) {
			$errors['password'] = 'Password must contain at least 1 uppercase letter.';
		} elseif (!preg_match('/[a-z]/', $password)) {
			$errors['password'] = 'Password must contain at least 1 lowercase letter.';
		} elseif (!preg_match('/[0-9]/', $password)) {
			$errors['password'] = 'Password must contain at least 1 number.';
		} elseif (!preg_match('/[^A-Za-z0-9]/', $password)) {
			$errors['password'] = 'Password must contain at least 1 symbol.';
		}

		if (empty($confirmation)) {
			$errors['password_confirmation'] = 'Please confirm the password.';
		} elseif ($password !== $confirmation) {
			$errors['password_confirmation'] = 'Passwords do not match.';
		}

		if (!empty($errors)) {
			view('users.reset-password', [
				'errors' => $errors,
				'old' => $postParams,
			]);
			return;
		}

		if (!$this->user->resetPassword($id, $password)) {
			view('users.reset-password', [
				'errors' => [
					'general' => 'Failed to reset the password.',
				],
				'old' => [],
			]);
			return;
		}

		(new AuditLog($this->conn))->log('password_change', null, $id);

		$_SESSION['success'] = 'Password reset successfully for ' . $user['name'] . '.';

		route('users');
		exit;
	}
}
