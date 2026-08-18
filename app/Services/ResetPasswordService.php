<?php

namespace App\Services;

use App\Config\Database;
use App\Models\ForgotPassword;
use App\Models\User;
use DateTime;
use RuntimeException;

class ResetPasswordService
{
    private User $userModel;
    private ForgotPassword $forgotPassword;

    public function __construct(
        User $userModel,
        ForgotPassword $forgotPassword
    ) {
        $this->userModel = $userModel;
        $this->forgotPassword = $forgotPassword;
    }

    public function verifyRequest(
        int $id,
        string $code
    ): array {
        $reset = $this->forgotPassword->findById($id);

        if (!$reset) {
            throw new RuntimeException('Reset request not found.');
        }

        $now = new DateTime();

        if ($reset['expires_at'] < $now->format('Y-m-d H:i:s')) {
            throw new RuntimeException('Link expired.');
        }

        if (!password_verify($code, $reset['hash'])) {
            throw new RuntimeException('Invalid link.');
        }

        return $reset;
    }

    public function resetPassword(
        int $userId,
        string $password
    ): void {
        $user = ($this->userModel->find($userId))[0] ?? null;

        if (!$user) {
            throw new RuntimeException('User not found.');
        }

        $this->userModel->update($user['id'], [
            'password' => $password,
        ]);
    }

    public function removeToken(int $id) {
        $conn = (new Database())->getConnection();

        $stmt = $conn->prepare('delete from forgot_password where id = ?');
        return $stmt->execute([$id]);
    }
}