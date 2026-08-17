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
        $stmt = $this->conn->query('
			select u.id, u.name, u.email, dep.name as department, des.name as designation
			from users u

			join departments dep
			on u.department_id = dep.id
			join designations des
			on u.designation_id = des.id

			where deleted_at is not null
		');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        view('users.deleted', ['users' => $users]);
    }

    public function restore(int $userId)
    {
        if (empty($userId)) {
            view(404);
            exit;
        }

        $stmt = $this->conn->prepare('UPDATE users set deleted_at = null where id = ?');
        if (!$stmt->execute([$userId])) {
            view(404);
            exit;
        }

        $_SESSION['success'] = "User #{$userId} restored successfully.";
        route('users');
    }
}
