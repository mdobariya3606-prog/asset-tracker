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
		middleware('auth');
		middleware('admin');

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
		middleware('auth');
		middleware('admin');
		
		view('departments.create');
	}
}
