<?php

namespace App\Controllers\User;

use App\Models\User;
use PDO;

class ProfileController
{
	private User $user;

	public function __construct(PDO $conn)
	{
		$this->user = new User($conn);
	}

	public function show(array $getParams): void
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to view a user profile.';
			header('Location: index.php?route=login');
			exit;
		}

		$viewerRole = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');

		$id = (int)($getParams['id'] ?? 0);
		$user = $this->user->find($id)[0] ?? null;
		if ($user === null) {
			$_SESSION['login_error'] = 'User not found.';
			header('Location: index.php?route=users');
			exit;
		}

		$targetRole = strtoupper($user['role'] ?? 'EMPLOYEE');
		$canEditProfile = $viewerRole === 'ADMIN' || ($viewerRole === 'MANAGER' && $targetRole !== 'ADMIN');
		$canManageResetOrDelete = false;
		if ($viewerRole === 'ADMIN') {
			$canManageResetOrDelete = true;
		} elseif ($viewerRole === 'MANAGER') {
			$canManageResetOrDelete = $targetRole !== 'ADMIN';
		} elseif ($viewerRole === 'HR') {
			$canManageResetOrDelete = $targetRole === 'EMPLOYEE';
		}

		view('users.profile', [
			'user' => $user,
			'canEditProfile' => $canEditProfile,
			'canManageResetOrDelete' => $canManageResetOrDelete
		]);
	}
}