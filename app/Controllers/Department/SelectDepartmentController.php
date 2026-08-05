<?php

namespace App\Controllers\Department;

use App\Models\Department;
use App\Models\User;

class SelectDepartmentController
{
	private \PDO $conn;
	private Department $department;

	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->department = new Department($conn);
	}

	public function index(array $getParams)
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to view departments.';
			header('Location: index.php?route=login');
			exit;
		}
		if (isset($getParams['id'])) {
			$departments = $this->department->find($getParams['id']);
			if (empty($departments)) {
				$message = "Department {$getParams['id']} does not exists.";
			}
		} else {
			$departments = $this->department->all();
		}

		// Render dashboard identity and access controls from the latest database
		// record instead of relying on values saved at sign-in.
		$dashboardUser = (new User($this->conn))->dashboardUser();
		$dashboardUserRole = strtoupper($dashboardUser['role'] ?? 'EMPLOYEE');

		require '../resources/views/departments/select.php';
	}

	public function all()
	{
		return $this->department->all();
	}
}