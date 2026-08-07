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
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in.';
			route('login');
			exit;
		}
		if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
			view('403');
			exit;
		}
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
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to add a designation.';
			route('login');
			exit;
		}
		if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
			view('403');
			exit;
		}

		view('designations.create');
		exit;
	}
}