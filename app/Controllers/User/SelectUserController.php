<?php

namespace App\Controllers\User;

use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use PDO;

class SelectUserController
{
	private PDO $conn;
	private User $userModel;

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
		$this->userModel = new User($conn);
	}

	public function index(array $getParams)
	{
		if (empty($_SESSION['user_id'])) {
			$_SESSION['login_error'] = 'Please sign in to view the users list.';
			header('Location: index.php?route=login');
			exit;
		}

		// Render dashboard identity and access controls from the latest database
		// record instead of relying on values saved at sign-in.
		$dashboardUser = $this->userModel->dashboardUser();
		$dashboardUserRole = strtoupper($dashboardUser['role'] ?? 'EMPLOYEE');

		$search = trim($getParams['search'] ?? '');
		$page = (int)($getParams['page'] ?? 1);
		if ($page < 1) {
			$page = 1;
		}
		$perPage = 5;

		$sort = trim($getParams['sort'] ?? 'id');
		$order = trim($getParams['order'] ?? 'asc');

		$departmentId = isset($getParams['department_id']) && $getParams['department_id'] !== '' ? (int)$getParams['department_id'] : null;
		$designationId = isset($getParams['designation_id']) && $getParams['designation_id'] !== '' ? (int)$getParams['designation_id'] : null;

		$activeDeptName = null;
		if ($departmentId !== null) {
			$activeDept = (new Department($this->conn))->find($departmentId);
			$activeDeptName = $activeDept[0]['name'] ?? null;
		}

		$activeDesignName = null;
		if ($designationId !== null) {
			$activeDesig = (new Designation($this->conn))->find($designationId);
			$activeDesignName = $activeDesig[0]['name'] ?? null;
		}

		$totalUsers = $this->userModel->count($search, $departmentId, $designationId);
		$totalPages = (int)ceil($totalUsers / $perPage);

		if ($page > $totalPages && $totalPages > 0) {
			$page = $totalPages;
		}

		$users = $this->userModel->paginate($page, $perPage, $search, $sort, $order, $departmentId, $designationId);

		if (empty($users)) {
			if ($search !== '' || $departmentId !== null || $designationId !== null) {
				$message = "No users found matching your active filters or search query.";
			} else {
				$message = "Users not found";
			}
		}

		view('users.select', [
			'dashboardUser' => $dashboardUser,
			'dashboardUserRole' => $dashboardUserRole,
			'search' => $search,
			'page' => $page,
			'perPage' => $perPage,
			'sort' => $sort,
			'order' => $order,
			'departmentId' => $departmentId,
			'designationId' => $designationId,
			'activeDeptName' => $activeDeptName,
			'activeDesignName' => $activeDesignName,
			'totalUsers' => $totalUsers,
			'totalPages' => $totalPages,
			'users' => $users,
			'message' => $message ?? null,
		]);
	}
}