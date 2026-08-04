<?php

namespace App\Controllers\User;

use App\Models\User;
use PDO;

class ResetPasswordController
{
    private PDO $conn;
    private User $user;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
        $this->user = new User($conn);
    }

    public function edit(array $getParams): void
    {
        $this->authorizeAdmin();

        $id = (int) ($getParams['id'] ?? 0);
        $user = $this->user->find($id)[0] ?? null;
        if ($user === null) {
            $_SESSION['login_error'] = 'User not found.';
            header('Location: index.php?route=users');
            exit;
        }

        $errors = [];
        $old = [];
        require '../resources/views/users/reset-password.php';
    }

    public function store(array $getParams, array $postParams): void
    {
        $this->authorizeAdmin();

        $id = (int) ($getParams['id'] ?? 0);
        $user = $this->user->find($id)[0] ?? null;
        if ($user === null) {
            $_SESSION['login_error'] = 'User not found.';
            header('Location: index.php?route=users');
            exit;
        }

        $password = (string) ($postParams['password'] ?? '');
        $confirmation = (string) ($postParams['password_confirmation'] ?? '');
        $errors = [];
        if (strlen($password) < 6) {
            $errors['password'] = 'Password must be 6 or more characters.';
        }
        if ($password !== $confirmation) {
            $errors['password_confirmation'] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            $old = $postParams;
            require '../resources/views/users/reset-password.php';
            return;
        }

        if (!$this->user->resetPassword($id, $password)) {
            $errors['general'] = 'Failed to reset the password.';
            $old = [];
            require '../resources/views/users/reset-password.php';
            return;
        }

        $_SESSION['success'] = 'Password reset successfully for ' . $user['name'] . '.';
        header('Location: index.php?route=users');
        exit;
    }

    private function authorizeAdmin(): void
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['login_error'] = 'Please sign in to reset a password.';
            header('Location: index.php?route=login');
            exit;
        }
        if (($_SESSION['user_role'] ?? '') !== 'ADMIN') {
            require '../resources/views/errors/403.php';
            exit;
        }
    }
}
