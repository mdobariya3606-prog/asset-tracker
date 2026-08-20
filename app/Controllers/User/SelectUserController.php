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
			route('login');
			exit;
		}

		$dashboardUser = $this->userModel->dashboardUser();
		$dashboardUserRole = strtoupper($dashboardUser['role'] ?? 'EMPLOYEE');

		$search = trim($getParams['search'] ?? '');
		$page = (int)($getParams['page'] ?? 1);
		$page = max($page, 1);

		$perPage = 5;

		$sort = trim($getParams['sort'] ?? 'id');
		$order = trim($getParams['order'] ?? 'asc');

		$departmentId = (
			isset($getParams['department_id']) &&
			$getParams['department_id'] !== ''
		)
			? (int)$getParams['department_id']
			: null;

		$designationId = (
			isset($getParams['designation_id']) &&
			$getParams['designation_id'] !== ''
		)
			? (int)$getParams['designation_id']
			: null;

		$roleFilter = null;

		if (
			$dashboardUserRole === 'ADMIN' &&
			isset($getParams['role']) &&
			trim($getParams['role']) !== ''
		) {
			$roleFilter = strtoupper(trim($getParams['role']));
		}

		$departments = (new Department($this->conn))->all();
		$designations = (new Designation($this->conn))->all();
		$roles = ['ADMIN', 'HR', 'MANAGER', 'EMPLOYEE'];

		$activeDeptName = null;

		if ($departmentId !== null) {
			$activeDept = (new Department($this->conn))->find($departmentId);
			$activeDeptName = $activeDept[0]['name'] ?? null;
		}

		$activeDesigName = null;

		if ($designationId !== null) {
			$activeDesign = (new Designation($this->conn))->find($designationId);
			$activeDesigName = $activeDesign[0]['name'] ?? null;
		}

		$totalUsers = $this->userModel->count(
			$search,
			$departmentId,
			$designationId,
			$roleFilter
		);

		$totalPages = (int)ceil($totalUsers / $perPage);

		if ($page > $totalPages && $totalPages > 0) {
			$page = $totalPages;
		}

		$users = $this->userModel->paginate(
			$page,
			$perPage,
			$search,
			$sort,
			$order,
			$departmentId,
			$designationId,
			$roleFilter
		);

		$message = null;

		if (empty($users)) {
			if (
				$search !== '' ||
				$departmentId !== null ||
				$designationId !== null ||
				$roleFilter !== null
			) {
				$message = 'No users found matching your active filters or search query.';
			} else {
				$message = 'Users not found';
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
			'roleFilter' => $roleFilter,
			'departments' => $departments,
			'designations' => $designations,
			'roles' => $roles,
			'activeDeptName' => $activeDeptName,
			'activeDesigName' => $activeDesigName,
			'totalUsers' => $totalUsers,
			'totalPages' => $totalPages,
			'users' => $users,
			'message' => $message,
		]);
	}
}
