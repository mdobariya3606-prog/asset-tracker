<?php

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    route('login');
    exit;
}

// Session timeout duration: 30 minutes (1800 seconds)
$timeout = (60 * 30);

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    // 1. Destroy existing active session completely
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();

    // 2. Create a new session only for storing the flash expiration message
    session_start();
    session_regenerate_id(true);
    $_SESSION['login_error'] = 'Your session has expired. Please login again.';

    // 3. Redirect user to login page and stop script execution
    route('login');
    exit;
}

// If session is still valid, update last activity timestamp
$_SESSION['last_activity'] = time();
