<?php

namespace App\Controllers;

use App\Models\Dashboard;
use App\Models\User;
use PDO;

class DashboardController
{
    public function __construct(private PDO $conn)
    {
    }

    public function data(): void
    {
        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Authentication required.']);
            exit;
        }

        $user = (new User($this->conn))->find((int) $_SESSION['user_id'])[0] ?? null;
        if (!$user || !empty($user['deleted_at'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Dashboard access denied.']);
            exit;
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
            (new Dashboard($this->conn))->data((int) $user['id'], (string) $user['role']),
            JSON_THROW_ON_ERROR
        );
        exit;
    }
}
