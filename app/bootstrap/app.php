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

    if ($currentUser === null) {
        session_unset();
    } else {
        $_SESSION['user_name'] = $currentUser['name'];
        $_SESSION['user_email'] = $currentUser['email'];
        $_SESSION['user_role'] = $currentUser['role'];
    }
}
