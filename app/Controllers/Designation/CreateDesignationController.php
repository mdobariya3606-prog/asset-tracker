<?php

namespace App\Controllers\Designation;

use App\Models\Designation;
use PDO;

class CreateDesignationController
{
	public Designation $designation;
	private PDO $conn;

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
		$this->designation = new Designation($conn);
	}

	public function store(array $designation)
	{
		middleware('auth');
		middleware('admin');

		$errors = $this->designation->validate($designation);

		if (!empty($errors)) {
			view('designations.create', ['errors' => $errors]);
			exit;
		}

		$this->designation->create($designation);
		route('designations');
		exit;
	}

	public function create()
	{
		middleware('auth');
		middleware('admin');

		view('designations.create');
		exit;
	}
}
