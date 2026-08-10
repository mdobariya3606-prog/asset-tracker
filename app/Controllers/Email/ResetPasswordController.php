<?php 

namespace App\Controllers\Email;

use App\Models\User;
use DateTime;
use PDO;

class ResetPasswordController {
    private PDO $conn;
    private User $userModel;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
        $this->userModel = new User($conn);
    }

    public function resetPassword(array $data) {
        $id = $data['id'];
        $code = $data['code'];

        $result = $this->findOrFail($id);
        $this->verifyRequest($code, $result);

        view('reset-password');
        exit;
    }

    public function updatePassword(array $getData, array $postData) {
        
        $id = $getData['id'];
        $code = $getData['code'];

        $result = $this->findOrFail($id);
        $this->verifyRequest($code, $result);

        $user = ($this->userModel->find($result['user_id']))[0];

        if (!$user) {
            view('404');
            exit;
        }

        $password = $postData['password'] ?? '';
        $confirm_password = $postData['confirm_password'] ?? '';

        $errors = [];
        if (empty($password)) {
            $errors['password'] = 'New Password is required.';
        } else if (empty($confirm_password)) {
            $errors['confirm_password'] = 'Please confirm your password..';
        } else if (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters long.';        
        } else if ($password !== $confirm_password) {
            $errors['confirm_password'] = 'Password do not match';
        }

        if (!empty($errors)) {
             view('reset-password', ['errors' => $errors]);
        }

        $this->userModel->update($user['id'], ['password' => $password]);

        $_SESSION['success'] = 'Password updated successfully.';
        route($_SESSION['user_id'] ? 'users' : 'login');
        exit;
    }

    public function findOrFail($id) {
        $stmt = $this->conn->prepare('select * from forgot_password where id = ?');
        $stmt->execute([$id]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$result) {
            view('404');
            exit;
        }

        return $result[0];
    }   
    
    public function verifyRequest(string $code, array $result) {
        $expiresAt = $result['expires_at'];
        $now = (new DateTime())->format('Y-m-d H:i:s');

        if ($expiresAt < $now) {
            view('404', [
                'message' => 'Link expired',
            ]);
            exit;
        }

        if (!password_verify($code, $result['hash'])) {
            view('403', ['message' => 'Invalid link']);
        }
    }
}