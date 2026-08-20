<?php

namespace App\Controllers\Department;

use App\helpers\Csrf;
use App\Models\Department;
use PDO;

class CreateDepartmentController
{
	/* =========================================================
	 * PROPERTIES
	 * ========================================================= */

	public Department $department;
	private PDO $conn;

	/* =========================================================
	 * CONSTRUCTOR
	 * ========================================================= */

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
		$this->department = new Department($conn);
	}

	/* =========================================================
	 * CREATE DEPARTMENT
	 * ========================================================= */

	public function create()
	{
		middleware('auth');
		middleware('admin');

		view('departments.create');
	}

	/* =========================================================
	 * STORE DEPARTMENT
	 * ========================================================= */

	public function store(array $department)
	{
		if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
			view(403);
			exit;
		}

		middleware('auth');
		middleware('admin');

		$errors = $this->department->validate($department);

		if (empty($errors)) {
			$this->department->create($department);

			$_SESSION['success'] = 'Department created successfully';

			route('departments');
			exit;
		}

		view('departments.create', [
			'errors' => $errors,
		]);
	}
}
