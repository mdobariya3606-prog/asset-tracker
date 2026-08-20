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
		middleware('auth');

		$viewerRole = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');

		$id = (int)($getParams['id'] ?? 0);
		$user = $this->user->find($id)[0] ?? null;

		if ($user === null) {
			$_SESSION['login_error'] = 'User not found.';
			route('users');
			exit;
		}

		$targetRole = strtoupper($user['role'] ?? 'EMPLOYEE');
		$canEditProfile = (
			$viewerRole === 'ADMIN' ||
			($viewerRole === 'MANAGER' && $targetRole !== 'ADMIN')
		);

		$isDeleted = $user['deleted_at'];

		$canManageResetOrDelete = false;

		if ($viewerRole === 'ADMIN') {
			$canManageResetOrDelete = true;
		} elseif ($viewerRole === 'HR') {
			$canManageResetOrDelete = $targetRole === 'EMPLOYEE';
		}

		view('users.profile', [
			'user' => $user,
			'canEditProfile' => $canEditProfile,
			'canManageResetOrDelete' => $canManageResetOrDelete,
			'isDeleted' => $isDeleted,
		]);
	}
}
