<?php

namespace App\Controllers;

class AuthController
{
	public function onlyAdminOrManager()
	{
		if ($_SESSION['user_role'] !== 'ADMIN' || $_SESSION['user_role'] !== 'MANAGER') {
			require '';
		}
	}
}