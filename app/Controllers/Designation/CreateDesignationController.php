<?php

namespace App\Controllers\Designation;

use App\helpers\Csrf;
use App\Models\Department;
use App\Models\Designation;
use PDO;

class CreateDesignationController
{
	/* =========================================================
	 * PROPERTIES
	 * ========================================================= */

	public Designation $designation;
	private PDO $conn;

	/* =========================================================
	 * CONSTRUCTOR
	 * ========================================================= */

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
		$this->designation = new Designation($conn);
	}

	/* =========================================================
	 * CREATE DESIGNATION
	 * ========================================================= */

	public function create()
	{
		middleware('auth');
		middleware('admin');

		view('designations.create', [
			'departments' => (new Department($this->conn))->all(),
		]);
		exit;
	}

	/* =========================================================
	 * STORE DESIGNATION
	 * ========================================================= */

	public function store(array $designation)
	{
		if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
			view(403);
			exit;
		}

		middleware('auth');
		middleware('admin');

		$errors = $this->designation->validate($designation);

		if (!empty($errors)) {
			view('designations.create', [
				'errors' => $errors,
				'old' => $designation,
				'departments' => (new Department($this->conn))->all(),
			]);
			exit;
		}

		$this->designation->create($designation);

		route('designations');
		exit;
	}
}
