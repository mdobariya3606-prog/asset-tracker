<?php

session_start();

use App\Config\Database;
use App\Models\User;

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$conn = (new Database())->getConnection();
$back = null;

if (!empty($_SESSION['user_id'])) {
    $currentUser = (new User($conn))->find((int) $_SESSION['user_id'])[0] ?? null;

    if ($currentUser === null || !empty($currentUser['deleted_at'])) {
        // Destroy session completely if account is deleted or missing
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

        // Start new session to flash message and redirect to login
        session_start();
        session_regenerate_id(true);
        $_SESSION['login_error'] = 'Your account has been deleted or deactivated. Please contact support.';
        
        route('login');
        exit;
    } else {
        $_SESSION['user_name'] = $currentUser['name'];
        $_SESSION['user_email'] = $currentUser['email'];
        $_SESSION['user_role'] = $currentUser['role'];
    }
}
