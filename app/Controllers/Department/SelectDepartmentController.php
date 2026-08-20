<?php

namespace App\Controllers\Department;

use App\Models\Department;
use App\Models\User;

class SelectDepartmentController
{
	/* =========================================================
	 * PROPERTIES
	 * ========================================================= */

	private \PDO $conn;
	private Department $department;

	/* =========================================================
	 * CONSTRUCTOR
	 * ========================================================= */

	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->department = new Department($conn);
	}

	/* =========================================================
	 * DEPARTMENT LIST
	 * ========================================================= */

	public function index(array $getParams)
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] =
				'Please sign in to view departments.';

			route('login');
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

		// Use the latest role from the database for dashboard access.
		$dashboardUser = (new User($this->conn))->dashboardUser();
		$dashboardUserRole = strtoupper(
			$dashboardUser['role'] ?? 'EMPLOYEE'
		);

		$role = $_SESSION['user_role'];

		view('departments.select', [
			'departments' => $departments,
			'dashboardUserRole' => $dashboardUserRole,
			'role' => $role,
		]);
	}
}
