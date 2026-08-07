<?php

namespace App\Controllers\User;

use App\Models\User;
use App\Services\Cache;
use App\Services\RateLimiter;
use PDO;

class LoginController
{
	private PDO $conn;
	private User $user;
	private RateLimiter $limiter;

	public function __construct(PDO $conn)
	{
		$this->conn = $conn;
		$this->user = new User($conn);
		$this->limiter = new RateLimiter(new Cache($conn));
	}

	public function showLoginForm()
	{
		$errors = [];
		if (isset($_SESSION['login_error'])) {
			$errors['general'] = $_SESSION['login_error'];
			unset($_SESSION['login_error']);
		}
		$old = [];
		$success = $_SESSION['login_success'] ?? null;
		unset($_SESSION['login_success']);

		view('login', [
			'errors' => $errors,
			'old' => $old,
			'success' => $success
		]);
	}

	public function login(array $postParams)
	{
		$result = $this->authenticate($postParams);

		if ($result['success']) {
			route('users');;
			exit;
		}

		$errors = $result['errors'];
		$old = $result['old'] ?? [];
		$success = null;
		view('login', [
			'errors' => $errors,
			'old' => $old,
			'success' => $success
		]);
	}

	/**
	 * Attempt to authenticate the user.
	 *
	 * @return array ['success' => bool, 'errors' => [], 'old' => [], 'user' => []]
	 */
	public function authenticate(array $data): array
	{
		// 1) Field-level validation
		$errors = $this->validateLogin($data);
		if (!empty($errors)) {
			return [
				'success' => false,
				'errors' => $errors,
				'old' => ['email' => $data['email'] ?? ''],
			];
		}

		$email = strtolower(trim($data['email']));
		$password = $data['password'];

		// Setup rate limiting keys based on IP & Email
		$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
		$throttleComboKey = 'login_throttle:' . md5($ipAddress . '|' . $email);
		$throttleIpKey = 'login_throttle_ip:' . md5($ipAddress);

		// 2) Check if IP-level block (10 attempts) has been exceeded
		if ($this->limiter->tooManyAttempts($throttleIpKey, 10)) {
			$seconds = $this->limiter->retriesIn($throttleIpKey);
			return [
				'success' => false,
				'errors' => ['general' => "Too many attempts. Please wait {$seconds} seconds."],
				'old' => ['email' => $data['email']],
			];
		}

		// 3) Check if combination-level block (5 attempts) has been exceeded
		if ($this->limiter->tooManyAttempts($throttleComboKey, 5)) {
			$seconds = $this->limiter->retriesIn($throttleComboKey);
			return [
				'success' => false,
				'errors' => ['general' => "Too many attempts. Please wait {$seconds} seconds."],
				'old' => ['email' => $data['email']],
			];
		}

		// 4) Find user and verify password (generic error to prevent enumeration)
		$user = $this->user->findByEmail($email);
		if (!$user || !password_verify($password, $user['password'])) {
			// Increment failed attempts on both keys
			$this->limiter->hit($throttleComboKey, 60);
			$this->limiter->hit($throttleIpKey, 60);

			return [
				'success' => false,
				'errors' => ['general' => 'Invalid email or password.'],
				'old' => ['email' => $data['email']],
			];
		}

		// 5) Success — clear attempts & set session
		$this->limiter->clear($throttleComboKey);
		$this->limiter->clear($throttleIpKey);

		$_SESSION['user_id'] = $user['id'];
		$_SESSION['user_name'] = $user['name'];
		$_SESSION['user_email'] = $user['email'];
		$_SESSION['user_role'] = $user['role'];
		$_SESSION['profile_image'] = $user['profile_image'];

		return [
			'success' => true,
			'errors' => [],
			'user' => $user,
		];
	}

	/**
	 * Validate login input fields.
	 */
	public function validateLogin(array $data): array
	{
		$errors = [];
		$email = trim($data['email'] ?? '');
		$password = $data['password'] ?? '';

		if (empty($email)) {
			$errors['email'] = 'Email is required.';
		} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errors['email'] = 'Please enter a valid email address.';
		}

		if (empty($password)) {
			$errors['password'] = 'Password is required.';
		}

		return $errors;
	}

	public function signout()
	{
		$this->logout();
		route('login');
		exit;
	}

	/**
	 * Log the user out.
	 */
	public function logout(): void
	{
		$_SESSION = [];
		if (ini_get('session.use_cookies')) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params['path'], $params['domain'],
				$params['secure'], $params['httponly']
			);
		}
		session_destroy();
	}
}