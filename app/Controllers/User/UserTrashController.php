<?php

namespace App\Controllers\User;

use App\Models\User;
use PDO;

class UserTrashController
{
    private PDO $conn;
    private User $userModel;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
        $this->userModel = new User($conn);
    }

    public function trash()
    {
        middleware('auth');
        middleware('hr');

        $stmt = $this->conn->query('
			SELECT
				u.id,
				u.name,
				u.email,
				dep.name AS department,
				des.name AS designation
			FROM users u
			JOIN departments dep ON u.department_id = dep.id
			JOIN designations des ON u.designation_id = des.id
			WHERE u.deleted_at IS NOT NULL
		');

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        view('users.deleted', [
            'users' => $users,
        ]);
    }

    public function restore(int $userId)
    {
        if (empty($userId)) {
            view(404);
            exit;
        }

        $stmt = $this->conn->prepare(
            'UPDATE users SET deleted_at = NULL WHERE id = ?'
        );

        if (!$stmt->execute([$userId])) {
            view(404);
            exit;
        }

        $_SESSION['success'] = "User #{$userId} restored successfully.";

        route('users');
    }
}
