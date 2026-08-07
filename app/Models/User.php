<?php

namespace App\Models;

use PDO;

class User
{
	private array $errors = [];
	private PDO $conn;

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
	}

	public function create(array $user)
	{
		$stmt = $this->conn->prepare('INSERT INTO users (name, email, mobile, department_id, designation_id, role, password, profile_image) VALUES (:name, :email, :mobile, :department_id, :designation_id, :role, :password, :profile_image)');
		$stmt->execute([
			'name' => trim($user['name'] ?? ''),
			'email' => strtolower(trim($user['email'] ?? '')),
			'mobile' => trim($user['mobile'] ?? ''),
			'department_id' => $user['department_id'] ?? null,
			'designation_id' => $user['designation_id'] ?? null,
			'role' => strtoupper(trim($user['role'] ?? 'EMPLOYEE')),
			'password' => password_hash($user['password'] ?? '', PASSWORD_DEFAULT),
			'profile_image' => $user['profile_image'] ?? null,
		]);
		return $this->conn->lastInsertId();
	}

	public function update($id, array $user)
	{
		$params = ['id' => $id];
		$setClauses = [];

		// Full-field update (name, email, mobile, etc.) only when name is present
		if (isset($user['name'])) {
			$setClauses[] = 'name = :name';
			$params['name'] = trim($user['name']);
		}
		if (isset($user['email'])) {
			$setClauses[] = 'email = :email';
			$params['email'] = strtolower(trim($user['email']));
		}
		if (isset($user['mobile'])) {
			$setClauses[] = 'mobile = :mobile';
			$params['mobile'] = trim($user['mobile']);
		}
		if (isset($user['department_id'])) {
			$setClauses[] = 'department_id = :department_id';
			$params['department_id'] = $user['department_id'];
		}
		if (isset($user['designation_id'])) {
			$setClauses[] = 'designation_id = :designation_id';
			$params['designation_id'] = $user['designation_id'];
		}
		if (isset($user['role'])) {
			$setClauses[] = 'role = :role';
			$params['role'] = strtoupper(trim($user['role']));
		}
		// profile_image: support explicit null (remove) or a filename
		if (array_key_exists('profile_image', $user)) {
			$setClauses[] = 'profile_image = :profile_image';
			$params['profile_image'] = $user['profile_image'];
		}
		if (!empty($user['password'])) {
			$setClauses[] = 'password = :password';
			$params['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
		}

		if (empty($setClauses)) {
			return false;
		}

		$sql = 'UPDATE users SET ' . implode(', ', $setClauses) . ' WHERE id = :id';
		$stmt = $this->conn->prepare($sql);
		return $stmt->execute($params);
	}

	public function delete($id): bool
	{
		$stmt = $this->conn->prepare('DELETE FROM users WHERE id = ?');
		return $stmt->execute([$id]);
	}

	/** Reset a user's password without requiring the previous password. */
	public function resetPassword(int $id, string $password): bool
	{
		$stmt = $this->conn->prepare('UPDATE users SET password = :password WHERE id = :id');
		return $stmt->execute([
			'password' => password_hash($password, PASSWORD_DEFAULT),
			'id' => $id,
		]);
	}

	public function validate(array $user, bool $isEdit = false, ?int $excludeId = null): array
	{
		$this->errors = [];
		$name = trim($user['name'] ?? '');
		$email = trim($user['email'] ?? '');
		$mobile = trim($user['mobile'] ?? '');
		$department_id = $user['department_id'] ?? '';
		$designation_id = $user['designation_id'] ?? '';
		$role = trim($user['role'] ?? '');
		if ($role === '') {
			$role = 'EMPLOYEE';
		}
		$password = $user['password'] ?? '';
		$confirm_password = $user['confirm_password'] ?? '';
		// Name validation
		if (empty($name)) {
			$this->errors['name'] = 'Name is required.';
		} elseif (strlen($name) > 50) {
			$this->errors['name'] = 'Name must be less than 50 characters.';
		} elseif (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
			$this->errors['name'] = 'Name can only contain letters and spaces.';
		}
		// Email validation
		if (empty($email)) {
			$this->errors['email'] = 'Email is required.';
		} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->errors['email'] = 'Email format is invalid.';
		} elseif ($this->emailExists($email, $excludeId)) {
			$this->errors['email'] = 'Email is already in use.';
		}
		// Mobile validation
		if (empty($mobile)) {
			$this->errors['mobile'] = 'Mobile number is required.';
		} elseif (!preg_match('/^\d{10}$/', $mobile)) {
			$this->errors['mobile'] = 'Mobile number must be 10 digits.';
		} elseif ($this->mobileExists($mobile, $excludeId)) {
			$this->errors['mobile'] = 'Mobile number is already in use.';
		}
		// Department validation
		if (empty($department_id)) {
			$this->errors['department_id'] = 'Department is required.';
		} elseif (!is_numeric($department_id)) {
			$this->errors['department_id'] = 'Please select a department.';
		}
		// Designation validation
		if (empty($designation_id)) {
			$this->errors['designation_id'] = 'Designation is required.';
		} elseif (!is_numeric($designation_id)) {
			$this->errors['designation_id'] = 'Please select a designation.';
		}
		// Role validation
		if (empty($role)) {
			$this->errors['role'] = 'Role is required.';
		} elseif (!in_array(strtoupper($role), ['ADMIN', 'HR', 'MANAGER', 'EMPLOYEE'])) {
			$this->errors['role'] = 'Please select a valid role.';
		}
		// Password & Confirm password validation
		if ($isEdit) {
			if (!empty($password) || !empty($confirm_password)) {
				if (empty($password)) {
					$this->errors['password'] = 'Password is required.';
				} elseif (strlen($password) < 6) {
					$this->errors['password'] = 'Password must be 6 or more characters.';
				}
				if (empty($confirm_password)) {
					$this->errors['confirm_password'] = 'Confirm password is required.';
				} elseif ($password !== $confirm_password) {
					$this->errors['confirm_password'] = 'Passwords do not match.';
				}
			}
		} else {
			if (empty($password)) {
				$this->errors['password'] = 'Password is required.';
			} elseif (strlen($password) < 6) {
				$this->errors['password'] = 'Password must be 6 or more characters.';
			}
			if (empty($confirm_password)) {
				$this->errors['confirm_password'] = 'Confirm password is required.';
			} elseif ($password !== $confirm_password) {
				$this->errors['confirm_password'] = 'Passwords do not match.';
			}
		}
		return $this->errors;
	}

	public function emailExists(string $email, ?int $excludeId = null): bool
	{
		$sql = 'SELECT COUNT(*) FROM users WHERE email = ?';
		$params = [strtolower(trim($email))];
		if ($excludeId !== null) {
			$sql .= ' AND id != ?';
			$params[] = $excludeId;
		}
		$stmt = $this->conn->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchColumn() > 0;
	}

	public function mobileExists(string $mobile, ?int $excludeId = null): bool
	{
		$sql = 'SELECT COUNT(*) FROM users WHERE mobile = ?';
		$params = [trim($mobile)];
		if ($excludeId !== null) {
			$sql .= ' AND id != ?';
			$params[] = $excludeId;
		}
		$stmt = $this->conn->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchColumn() > 0;
	}

	public function findByEmail(string $email): array|false
	{
		$stmt = $this->conn->prepare('SELECT * FROM users WHERE email = ?');
		$stmt->execute([strtolower(trim($email))]);
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function all()
	{
		$stmt = $this->conn->query(
			"SELECT u.*, d.name AS department_name, des.name AS designation_name
            FROM users u
            LEFT JOIN departments d ON u.department_id = d.id
            LEFT JOIN designations des ON u.designation_id = des.id"
		);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function paginate(int $page, int $perPage, string $search = '', string $sort = 'id', string $order = 'asc', ?int $departmentId = null, ?int $designationId = null): array
	{
		$offset = ($page - 1) * $perPage;
		$sortColumns = [
			'id' => 'u.id',
			'name' => 'u.name',
			'email' => 'u.email',
			'mobile' => 'u.mobile',
			'department' => 'd.name',
			'designation' => 'des.name',
			'role' => 'u.role',
		];
		$orderByColumn = $sortColumns[$sort] ?? 'u.id';
		$orderDirection = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
		$sql = "
            SELECT u.*, d.name AS department_name, des.name AS designation_name
            FROM users u
            LEFT JOIN departments d ON u.department_id = d.id
            LEFT JOIN designations des ON u.designation_id = des.id
        ";
		$whereClauses = [];
		$params = [];
		if ($search !== '') {
			$whereClauses[] = '(u.name LIKE ? OR u.email LIKE ? OR u.mobile LIKE ? OR d.name LIKE ? OR des.name LIKE ?)';
			$searchTerm = '%' . $search . '%';
			$params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
		}
		if ($departmentId !== null) {
			$whereClauses[] = 'u.department_id = ?';
			$params[] = $departmentId;
		}
		if ($designationId !== null) {
			$whereClauses[] = 'u.designation_id = ?';
			$params[] = $designationId;
		}
		if (!empty($whereClauses)) {
			$sql .= ' WHERE ' . implode(' AND ', $whereClauses);
		}
		$sql .= " ORDER BY {$orderByColumn} {$orderDirection} LIMIT ? OFFSET ?";
		$stmt = $this->conn->prepare($sql);
		$index = 1;
		foreach ($params as $param) {
			$stmt->bindValue($index++, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
		}
		$stmt->bindValue($index++, $perPage, PDO::PARAM_INT);
		$stmt->bindValue($index++, $offset, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function count(string $search = '', ?int $departmentId = null, ?int $designationId = null): int
	{
		$sql = "
            SELECT COUNT(*)
            FROM users u
            LEFT JOIN departments d ON u.department_id = d.id
            LEFT JOIN designations des ON u.designation_id = des.id
        ";
		$whereClauses = [];
		$params = [];
		if ($search !== '') {
			$whereClauses[] = '(u.name LIKE ? OR u.email LIKE ? OR u.mobile LIKE ? OR d.name LIKE ? OR des.name LIKE ?)';
			$searchTerm = '%' . $search . '%';
			$params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
		}
		if ($departmentId !== null) {
			$whereClauses[] = 'u.department_id = ?';
			$params[] = $departmentId;
		}
		if ($designationId !== null) {
			$whereClauses[] = 'u.designation_id = ?';
			$params[] = $designationId;
		}
		if (!empty($whereClauses)) {
			$sql .= ' WHERE ' . implode(' AND ', $whereClauses);
		}
		$stmt = $this->conn->prepare($sql);
		$index = 1;
		foreach ($params as $param) {
			$stmt->bindValue($index++, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
		}
		$stmt->execute();
		return (int)$stmt->fetchColumn();
	}

	public function dashboardUser()
	{
		$dashboardUser = $this->find((int)$_SESSION['user_id'])[0] ?? null;
		if ($dashboardUser === null) {
			session_unset();
			$_SESSION['login_error'] = 'Your account is no longer available.';
			route('login');
			exit;
		}

		return $dashboardUser;
	}

	public function find($id): array
	{
		$stmt = $this->conn->prepare(
			"SELECT u.*, d.name AS department_name, des.name AS designation_name
            FROM users u
            LEFT JOIN departments d ON u.department_id = d.id
            LEFT JOIN designations des ON u.designation_id = des.id
            WHERE u.id = ?"
		);
		$stmt->execute([$id]);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}