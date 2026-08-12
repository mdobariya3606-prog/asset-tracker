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
		require_once __DIR__ . '/../../Middleware/auth.php';
		require_once __DIR__ . '/../../Middleware/admin.php';

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
		require_once __DIR__ . '/../../Middleware/auth.php';
		require_once __DIR__ . '/../../Middleware/admin.php';

		view('designations.create');
		exit;
	}
}
