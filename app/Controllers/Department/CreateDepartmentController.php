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
			route('login');
			exit;
		}
		if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
			view('403');
			exit;
		}

		$errors = $this->department->validate($department);
		if (empty($errors)) {
			$this->department->create($department);
			$_SESSION['success'] = 'Department created successfully';

			route('departments');
			exit;
		}

		view('departments.create', ['errors' => $errors]);
	}

	public function create()
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to add a department.';
			route('login');
			exit;
		}
		if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
			view('403');
			exit;
		}
		view('departments.create');
	}
}