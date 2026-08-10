<?php

namespace App\Models;

use PDO;

class ForgotPassword
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->conn->prepare(
            'SELECT * FROM forgot_password WHERE id = ?'
        );

        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }
}