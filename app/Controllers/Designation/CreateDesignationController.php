<?php

namespace App\Controllers\Designation;

use App\Models\Department;
use App\Models\Designation;
use PDO;

class CreateDesignationController
{
	private PDO $conn;
	public Designation $designation;
	public function __construct(PDO $conn) {
		$this->conn = $conn;
		$this->designation = new Designation($conn);
	}

	public function create() {
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to add a designation.';
			header('Location: index.php?route=login');
			exit;
		}
		if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
			require '../resources/views/errors/403.php';
			exit;
		}
		require '../resources/views/designation/create.php';
	}

	public function store(array $designation) {
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in.';
			header('Location: index.php?route=login');
			exit;
		}
		if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
			require '../resources/views/errors/403.php';
			exit;
		}
		$this->designation->create($designation);

		header('Location: index.php?route=designations');
		exit;
	}
}