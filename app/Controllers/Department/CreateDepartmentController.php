<?php

namespace App\Controllers\Department;

use App\Models\Department;
use PDO;

class CreateDepartmentController
{
	public Department $department;
	private PDO $conn;

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
		$this->department = new Department($conn);
	}

	public function store(array $department)
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in.';
			header('Location: index.php?route=login');
			exit;
		}
		if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
			require '../resources/views/errors/403.php';
			exit;
		}

		$errors = $this->department->validate($department);
		if (empty($errors)) {
			$this->department->create($department);
			$_SESSION['success'] = 'Department created successfully';

			header('Location: index.php?route=departments');
			exit;
		}

		require '../resources/views/departments/create.php';
	}

	public function create()
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to add a department.';
			header('Location: index.php?route=login');
			exit;
		}
		if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
			require '../resources/views/errors/403.php';
			exit;
		}
		require '../resources/views/departments/create.php';
	}
}