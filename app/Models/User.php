<?php

namespace App\Models;

use PDO;

/**
 * User model — handles CRUD, authentication support, validation,
 * and listing/export queries for the users table.
 */
class User
{
	private array $errors = [];
	private PDO $conn;

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
	}

	/**
	 * Insert a new user. Password is hashed here — never store plaintext.
	 *
	 * @param array $user Raw user input (expects name, email, mobile,
	 *                     department_id, designation_id, role, password,
	 *                     profile_image)
	 * @return string Last inserted ID
	 */
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

	/**
	 * Partially update a user. Only fields present in $user are updated
	 * (dynamic SET clause), so callers can pass a subset of fields.
	 *
	 * Note: password is only updated if non-empty, so callers can omit
	 * it entirely to leave the existing password untouched.
	 *
	 * @param int $id
	 * @param array $user
	 * @return bool
	 */
	public function update($id, array $user)
	{
		$params = ['id' => $id];
		$setClauses = [];

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

		// profile_image: supports filename (string) or explicit null (when deleted)
		if (array_key_exists('profile_image', $user)) {
			$setClauses[] = 'profile_image = :profile_image';
			$params['profile_image'] = $user['profile_image'];
		}

		// Only touch password if a new one was actually supplied
		if (!empty($user['password'])) {
			$setClauses[] = 'password = :password';
			$params['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
		}

		// Nothing to update — avoid running an empty/invalid SQL statement
		if (empty($setClauses)) {
			return false;
		}

		$sql = 'UPDATE users SET ' . implode(', ', $setClauses) . ' WHERE id = :id';
		$stmt = $this->conn->prepare($sql);
		return $stmt->execute($params);
	}

	/**
	 * Soft delete: marks the user as deleted without removing the row.
	 */
	public function softDelete($id): bool
	{
		$stmt = $this->conn->prepare('UPDATE users SET deleted_at = now() WHERE id = ?');
		return $stmt->execute([$id]);
	}
	public function deletePermanantly($id): bool
	{
		$stmt = $this->conn->prepare('DELETE from users WHERE id = ?');
		if ($stmt->execute([$id])) {
			$_SESSION['success'] = "User #$id deleted permanantly.";
			return true;
		}

		return false;
	}

	/**
	 * Reset a user's password without requiring the previous password.
	 * Used for "forgot password" / admin-initiated reset flows.
	 */
	public function resetPassword(int $id, string $password): bool
	{
		$stmt = $this->conn->prepare('UPDATE users SET password = :password WHERE id = :id');
		return $stmt->execute([
			'password' => password_hash($password, PASSWORD_DEFAULT),
			'id' => $id,
		]);
	}

	/**
	 * Validate user input for create/edit forms, including password
	 * strength rules and profile image constraints.
	 *
	 * @param array $user Raw form input
	 * @param bool $isEdit If true, password/confirm_password are only
	 *                      validated when at least one is non-empty
	 *                      (i.e. user is changing their password)
	 * @param int|null $excludeId Current user's own ID, excluded from
	 *                             uniqueness checks (email/mobile) so
	 *                             editing your own record doesn't
	 *                             collide with yourself
	 * @param array|null $file Raw $_FILES['profile_image'] entry, if any
	 * @return array Field => error message. Empty array means valid.
	 */
	public function validate(array $user, bool $isEdit = false, ?int $excludeId = null, ?array $file = null): array
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

		// --- Name validation ---
		if (empty($name)) {
			$this->errors['name'] = 'Name is required.';
		} elseif (strlen($name) > 50) {
			$this->errors['name'] = 'Name must be less than 50 characters.';
		} elseif (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
			$this->errors['name'] = 'Name can only contain letters and spaces.';
		}

		// --- Email validation (format + uniqueness) ---
		if (empty($email)) {
			$this->errors['email'] = 'Email is required.';
		} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->errors['email'] = 'Email format is invalid.';
		} elseif ($this->emailExists($email, $excludeId)) {
			$this->errors['email'] = 'Email is already in use.';
		}

		// --- Mobile validation (format + uniqueness) ---
		if (empty($mobile)) {
			$this->errors['mobile'] = 'Mobile number is required.';
		} elseif (!preg_match('/^\d{10}$/', $mobile)) {
			$this->errors['mobile'] = 'Mobile number must be 10 digits.';
		} elseif ($this->mobileExists($mobile, $excludeId)) {
			$this->errors['mobile'] = 'Mobile number is already in use.';
		}

		// --- Department validation ---
		if (empty($department_id)) {
			$this->errors['department_id'] = 'Department is required.';
		} elseif (!is_numeric($department_id)) {
			$this->errors['department_id'] = 'Please select a department.';
		}

		// --- Designation validation ---
		if (empty($designation_id)) {
			$this->errors['designation_id'] = 'Designation is required.';
		} elseif (!is_numeric($designation_id)) {
			$this->errors['designation_id'] = 'Please select a designation.';
		}

		// --- Role validation ---
		if (empty($role)) {
			$this->errors['role'] = 'Role is required.';
		} elseif (!in_array(strtoupper($role), ['ADMIN', 'HR', 'MANAGER', 'EMPLOYEE'])) {
			$this->errors['role'] = 'Please select a valid role.';
		}

		// --- Password & Confirm password validation ---
		// Shared strength rule: 8-30 chars, upper, lower, digit, symbol
		$validatePasswordStrength = function ($password) {
			if (strlen($password) < 8 || strlen($password) > 30) {
				return 'Password must be 8–30 characters long.';
			}
			if (!preg_match('/[A-Z]/', $password)) {
				return 'Password must contain at least 1 uppercase letter.';
			}
			if (!preg_match('/[a-z]/', $password)) {
				return 'Password must contain at least 1 lowercase letter.';
			}
			if (!preg_match('/[0-9]/', $password)) {
				return 'Password must contain at least 1 number.';
			}
			if (!preg_match('/[^A-Za-z0-9]/', $password)) {
				return 'Password must contain at least 1 symbol.';
			}
			return null;
		};

		// On create, password is always required.
		// On edit, only validate if the user is actually changing it.
		$needsPasswordCheck = $isEdit ? (!empty($password) || !empty($confirm_password)) : true;

		if ($needsPasswordCheck) {
			if (empty($password)) {
				$this->errors['password'] = 'Password is required.';
			} elseif ($err = $validatePasswordStrength($password)) {
				$this->errors['password'] = $err;
			}

			if (empty($confirm_password)) {
				$this->errors['confirm_password'] = 'Confirm password is required.';
			} elseif ($password !== $confirm_password) {
				$this->errors['confirm_password'] = 'Passwords do not match.';
			}
		}

		// --- Profile image validation (only runs if a file was actually uploaded) ---
		if (!empty($file) && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
			$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
			$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
			$maxFileSize = 2 * 1024 * 1024; // 2 MB

			$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

			// 1. Extension check (fast, but spoofable by renaming a file)
			if (!in_array($fileExtension, $allowedExtensions)) {
				$this->errors['profile_image'] = 'Only JPG, JPEG, PNG, and WEBP formats are allowed.';
			}
			// 2. Size check
			elseif ($file['size'] > $maxFileSize) {
				$this->errors['profile_image'] = 'Profile image size must not exceed 2MB.';
			}
			// 3. Real MIME-type check via file content (catches spoofed extensions)
			elseif (function_exists('finfo_open')) {
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mimeType = finfo_file($finfo, $file['tmp_name']);
				finfo_close($finfo);

				if (!in_array($mimeType, $allowedMimeTypes)) {
					$this->errors['profile_image'] = 'Invalid image file format.';
				}
			}
		} elseif (!empty($file) && isset($file['error']) && $file['error'] !== UPLOAD_ERR_NO_FILE) {
			// A file was attempted but PHP reported an upload error
			// (e.g. exceeded upload_max_filesize, partial upload, etc.)
			$this->errors['profile_image'] = 'An error occurred while uploading the profile image.';
		}

		return $this->errors;
	}

	/**
	 * Check if an email is already taken by another user.
	 * $excludeId lets the current user's own record be skipped
	 * (so editing your own profile without changing email doesn't fail).
	 */
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

	/**
	 * Check if a mobile number is already taken by another user.
	 */
	public function mobileExists(string $mobile, ?int $excludeId = null): bool
	{
		$sql = 'SELECT COUNT(*) FROM users WHERE mobile = ?';
		$params = [trim($mobile)];
		if ($excludeId) {
			$sql .= ' AND id != ?';
			$params[] = $excludeId;
		}
		$stmt = $this->conn->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchColumn() > 0;
	}

	/**
	 * Look up a user by email — used during login.
	 */
	public function findByEmail(string $email): array|false
	{
		$stmt = $this->conn->prepare('SELECT * FROM users WHERE email = ?');
		$stmt->execute([strtolower(trim($email))]);
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Fetch all users with department/designation names joined in.
	 * No pagination — use paginate() for listing pages.
	 */
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

	/**
	 * Fetch a page of users with optional search, sorting, and filters.
	 *
	 * @param int $page 1-based page number
	 * @param int $perPage Rows per page
	 * @param string $search Matched against name/email/mobile/department/designation
	 * @param string $sort Logical column name (mapped via $sortColumns allowlist)
	 * @param string $order 'asc' or 'desc'
	 * @param int|null $departmentId Filter by department
	 * @param int|null $designationId Filter by designation
	 */
	public function paginate(
		int $page,
		int $perPage,
		string $search = '',
		string $sort = 'id',
		string $order = 'asc',
		?int $departmentId = null,
		?int $designationId = null
	): array {
		$offset = ($page - 1) * $perPage;

		// Allowlist of sortable columns — prevents arbitrary column
		// injection via the $sort parameter
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
			WHERE u.deleted_at IS NULL 
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
			$sql .= ' AND ' . implode(' AND ', $whereClauses);
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

	/**
	 * Count total users matching the same filters used in paginate(),
	 * for computing total pages.
	 */
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

	/**
	 * Fetch the currently logged-in user's own record for dashboard use.
	 * Forces logout if the account no longer exists (e.g. deleted mid-session).
	 */
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

	/**
	 * Find a user by ID, with department/designation names joined in.
	 * Returns an array (possibly empty) rather than a single row —
	 * callers use $result[0].
	 */
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

	/**
	 * Store a "remember me" token for persistent login.
	 * NOTE: token is stored as-is here — see removeRememberToken()
	 * and findByRememberToken(), which expect a hashed token. Confirm
	 * the caller hashes $token before passing it in, or this is
	 * inconsistent with the other two methods.
	 */
	public function saveRememberToken($userId, $token)
	{
		$stmt = $this->conn->prepare('update users set remember_token = ? where id = ?');
		$stmt->execute([$token, $userId]);
	}

	/**
	 * Clear a "remember me" token (e.g. on logout).
	 * Expects the raw token and hashes it here before matching.
	 */
	public function removeRememberToken($token)
	{
		$stmt = $this->conn->prepare('update users set remember_token = null where remember_token = ?');
		$stmt->execute([hash('sha256', $token)]);
	}

	/**
	 * Look up a user by an already-hashed remember token.
	 */
	public function findByRememberToken($tokenHash)
	{
		$stmt = $this->conn->prepare('select * from users where remember_token = ? limit 1');
		$stmt->execute([$tokenHash]);

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Export users to PDF or Excel view. Employee-only export filters
	 * to role = EMPLOYEE.
	 *
	 * @param string $option 'pdf' or 'excel'
	 * @param string|null $role If 'employee', restricts export to employees only
	 */
	public function export($option = 'pdf', $role = null)
	{
		middleware('auth');

		$sql = "SELECT u.*, d.name AS department_name, des.name AS designation_name
				FROM users u
				LEFT JOIN departments d ON u.department_id = d.id
				LEFT JOIN designations des ON u.designation_id = des.id";

		if (strtolower(trim($role)) == 'employee') {
			$sql .= ' WHERE role = "EMPLOYEE"';
		}
		$sql .= ' ORDER BY role';

		$stmt = $this->conn->query($sql);

		$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (strtolower(trim($option)) == 'excel') {
			view('users.excel', ['users' => $users]);
			exit;
		}
		view('users.pdf', ['users' => $users]);
		exit;
	}
}
