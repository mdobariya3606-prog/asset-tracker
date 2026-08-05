<?php

namespace App\Controllers\Designation;

use App\Models\Designation;
use App\Models\User;

class SelectDesignationController
{
	private \PDO $conn;
	private Designation $designation;

	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->designation = new Designation($conn);
	}

	public function index(array $getParams)
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to view designations.';
			header('Location: index.php?route=login');
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

		// Render dashboard identity and access controls from the latest database
		// record instead of relying on values saved at sign-in.
		$dashboardUser = (new User($this->conn))->dashboardUser();
		$dashboardUserRole = strtoupper($dashboardUser['role'] ?? 'EMPLOYEE');

		$role = $_SESSION['user_role'];

		require '../resources/views/designation/select.php';
	}

	public function all()
	{
		return $this->designation->all();
	}
}