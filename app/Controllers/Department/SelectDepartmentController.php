<?php

namespace App\Controllers\Department;

use App\Models\Department;

class SelectDepartmentController
{
	private \PDO $conn;
	private Department  $department;

	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->department = new Department($conn);
	}

	public function all() {
		return $this->department->all();
	}

	public function index(array $getParams) {
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to view departments.';
			header('Location: index.php?route=login');
			exit;
		}
		if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
			require '../resources/views/errors/403.php';
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
		require '../resources/views/departments/select.php';
	}
}