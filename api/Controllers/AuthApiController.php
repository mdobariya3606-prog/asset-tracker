<?php

declare(strict_types=1);

namespace Api\Controllers;

use Api\Response;
use App\Models\User;

/** Provides JSON login and logout without rendering web views or redirects. */
final class AuthApiController extends BaseApiController
{
    public function handle(?string $action, string $method, array $query, array $body): never
    {
        if ($action === 'login') {
            if ($method !== 'POST') Response::error('METHOD_NOT_ALLOWED', 'Use POST for auth/login.', 405);
            // Look up the account and verify the stored password hash.
           
            $user = (new User($this->conn))->findByEmail((string)($body['email'] ?? ''));
            if (!$user) {
                Response::error('INVALID_CREDENTIALS', 'User not found.', 404);
            }
            if (!empty($user['deleted_at'])) {
                Response::error('INVALID_CREDENTIALS', 'User deleted.', 404);
            }
            if (!password_verify($body['password'], $user['password'])) {
                Response::error('INVALID_CREDENTIALS', 'Invalid email or password.', 401);
            }

            // if (
            //     !$user
            //     || !empty($user['deleted_at'])
            //     || !password_verify((string)($body['password'] ?? ''), $user['password'])
            // ) {
            //     Response::error('INVALID_CREDENTIALS', 'Invalid email or password.', 401);
            // }

            // Keep authentication compatible with the current session-based app.
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $user['session_id'] = session_id();

            unset($user['password'], $user['remember_token']);
            Response::send($user);
        }
        if ($action === 'logout') {
            if ($method !== 'POST') Response::error('METHOD_NOT_ALLOWED', 'Use POST for auth/logout.', 405);
            // Clear the same session used by the normal web application.
            $_SESSION = [];
            session_destroy();
            Response::send(['logged_out' => true]);
        }
        Response::error('NOT_FOUND', 'Auth API endpoint not found.', 404);
    }
}
