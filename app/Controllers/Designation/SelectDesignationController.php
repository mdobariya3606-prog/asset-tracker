<?php

namespace App\Controllers\Designation;

use App\Models\Department;
use App\Models\Designation;

class SelectDesignationController
{
	private \PDO $conn;
	private Designation $designation;

	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->designation = new Designation($conn);
	}

	public function all() {
		return $this->designation->all();
	}

	public function index(array $getParams) {
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to view designations.';
			header('Location: index.php?route=login');
			exit;
		}
		if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
			require '../resources/views/errors/403.php';
			exit;
		}
		if (isset($getParams['id'])) {
			$designations = $this->designation->find($getParams['id']);
			if (empty($designations)) {
				$message = "Designation {$getParams['id']} does not exists.";
			}
		} else {
			$designations = $this->designation->all();
		}
		require '../resources/views/designation/select.php';
	}
}