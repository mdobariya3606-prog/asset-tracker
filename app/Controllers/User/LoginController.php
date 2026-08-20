<?php

namespace App\Controllers\User;

use App\helpers\Csrf;
use App\Models\AuditLog;
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

	/* =========================================================
	 * LOGIN
	 * ========================================================= */

	public function showLoginForm()
	{
		if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
			$tokenHash = hash('sha256', $_COOKIE['remember_token']);
			$user = $this->user->findByRememberToken($tokenHash);

			if ($user) {
				session_regenerate_id();

				$_SESSION['user_id'] = $user['id'];
				$_SESSION['user_name'] = $user['name'];
				$_SESSION['user_email'] = $user['email'];
				$_SESSION['user_role'] = $user['role'];
				$_SESSION['profile_image'] = $user['profile_image'];
				$_SESSION['last_activity'] = time();

				(new AuditLog($this->conn))->log('LOGIN');

				route('users');
				exit;
			}

			setcookie('remember_token', '', time() - 3600, '/');
		}

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
			'success' => $success,
		]);
	}

	public function login(array $postParams)
	{
		if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
			view(403);
			exit;
		}

		$result = $this->authenticate($postParams);

		if ($result['success']) {
			(new AuditLog($this->conn))->log('LOGIN');

			route('users');
			exit;
		}

		$errors = $result['errors'];
		$old = $result['old'] ?? [];

		view('login', [
			'errors' => $errors,
			'old' => $old,
			'success' => null,
		]);
	}

	/**
	 * Attempt to authenticate the user.
	 *
	 * @return array ['success' => bool, 'errors' => [], 'old' => [], 'user' => []]
	 */
	public function authenticate(array $data): array
	{
		$errors = $this->validateLogin($data);

		if (!empty($errors)) {
			return [
				'success' => false,
				'errors' => $errors,
				'old' => [
					'email' => $data['email'] ?? '',
				],
			];
		}

		$email = strtolower(trim($data['email']));
		$password = $data['password'];

		$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

		$throttleComboKey = 'login_throttle:' . md5($ipAddress . '|' . $email);
		$throttleIpKey = 'login_throttle_ip:' . md5($ipAddress);

		if ($this->limiter->tooManyAttempts($throttleIpKey, 10)) {
			$seconds = $this->limiter->retriesIn($throttleIpKey);

			return [
				'success' => false,
				'errors' => [
					'general' => "Too many attempts. Please wait {$seconds} seconds.",
				],
				'old' => [
					'email' => $data['email'],
				],
			];
		}

		if ($this->limiter->tooManyAttempts($throttleComboKey, 5)) {
			$seconds = $this->limiter->retriesIn($throttleComboKey);

			return [
				'success' => false,
				'errors' => [
					'general' => "Too many attempts. Please wait {$seconds} seconds.",
				],
				'old' => [
					'email' => $data['email'],
				],
			];
		}

		$user = $this->user->findByEmail($email);

		if (
			!$user ||
			$user['deleted_at'] ||
			!password_verify($password, $user['password'])
		) {
			$this->limiter->hit($throttleComboKey, 300);
			$this->limiter->hit($throttleIpKey, 300);

			return [
				'success' => false,
				'errors' => [
					'general' => 'Invalid email or password.',
				],
				'old' => [
					'email' => $data['email'],
				],
			];
		}

		$this->limiter->clear($throttleComboKey);
		$this->limiter->clear($throttleIpKey);

		if ($data['remember'] === 'on') {
			$token = bin2hex(random_bytes(32));

			setcookie(
				'remember_token',
				$token,
				[
					'expires' => time() + (30 * 24 * 60 * 60),
					'path' => '/',
					'secure' => isset($_SERVER['HTTPS']),
					'httponly' => true,
					'samesite' => 'Lax',
				]
			);

			(new User($this->conn))->saveRememberToken(
				$user['id'],
				hash('sha256', $token)
			);
		}

		session_regenerate_id();

		$_SESSION['user_id'] = $user['id'];
		$_SESSION['user_name'] = $user['name'];
		$_SESSION['user_email'] = $user['email'];
		$_SESSION['user_role'] = $user['role'];
		$_SESSION['profile_image'] = $user['profile_image'];
		$_SESSION['last_activity'] = time();

		return [
			'success' => true,
			'errors' => [],
			'user' => $user,
		];
	}

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

	/* =========================================================
	 * LOGOUT
	 * ========================================================= */

	public function signout()
	{
		if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
			view(403);
			exit;
		}

		(new AuditLog($this->conn))->log('logout');

		$this->logout();

		route('login');
		exit;
	}

	public function logout(): void
	{
		if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
			view(403);
			exit;
		}

		if (isset($_COOKIE['remember_token'])) {
			$this->user->removeRememberToken($_COOKIE['remember_token']);

			setcookie(
				'remember_token',
				'',
				time() - 3600,
				'/'
			);
		}

		$_SESSION = [];

		if (ini_get('session.use_cookies')) {
			$params = session_get_cookie_params();

			setcookie(
				session_name(),
				'',
				time() - 42000,
				$params['path'],
				$params['domain'],
				$params['secure'],
				$params['httponly']
			);
		}

		session_destroy();
	}
}
