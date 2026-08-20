<?php

namespace App\Controllers\Designation;

use App\Models\Designation;
use App\Models\User;

class SelectDesignationController
{
	/* =========================================================
	 * PROPERTIES
	 * ========================================================= */

	private \PDO $conn;
	private Designation $designation;

	/* =========================================================
	 * CONSTRUCTOR
	 * ========================================================= */

	public function __construct(\PDO $conn)
	{
		$this->conn = $conn;
		$this->designation = new Designation($conn);
	}

	/* =========================================================
	 * DESIGNATION LIST
	 * ========================================================= */

	public function index(array $getParams)
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] =
				'Please sign in to view designations.';

			route('login');
			exit;
		}

		if (isset($getParams['id'])) {
			$designations = $this->designation->find($getParams['id']);

			if (empty($designations)) {
				$message =
					"Designation {$getParams['id']} does not exists.";
			}
		} else {
			$designations = $this->designation->all();
		}

		// Use the latest role from the database for dashboard access.
		$dashboardUser = (new User($this->conn))->dashboardUser();
		$dashboardUserRole = strtoupper(
			$dashboardUser['role'] ?? 'EMPLOYEE'
		);

		$role = $_SESSION['user_role'];

		view('designations.select', [
			'designations' => $designations,
			'dashboardUserRole' => $dashboardUserRole,
			'role' => $role,
		]);
	}
}
