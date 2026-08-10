<?php

namespace App\Controllers\Email;

use App\Config\Mail;
use App\Models\User;
use DateTime;
use PDO;

class ForgotPasswordEmail
{
    private PDO $conn;
    private User $userModel;
    private Mail $mail;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
        $this->userModel = new User($conn);
        $this->mail = new Mail();
    }

    public function sendResetPasswordMail($user_id = null)
    {
        if (!isset($_SESSION['user_id']) && !$user_id) {
            route('fp-mail');
            exit;
        }

        $user_id = $user_id ?? $_SESSION['user_id'];

        if ($this->isSent($user_id)) {
            $_SESSION['success'] = 'Mail already sent.';
            header('Location: index.php?route=users/edit&id=' . $_SESSION['user_id']);
            exit;
        }

        $user = $this->userModel->find($user_id);
        if (!$user) {
            view(404);
            exit;
        }

        $generatedCode = $this->generateFPHash($user_id);

        $link = "http://localhost/AssetTracker/public/index.php?route=reset-password&id={$generatedCode['id']}&code=" . urlencode($generatedCode['code']);

        $mailAddress = $user[0]['email'];

        if ($mailAddress) {
            $this->mail->send($mailAddress, 'Forgot password', $link);
        } else {
            view(404);
        }

        $_SESSION['success'] = 'Mail sent successfully.';

        if (empty($_SESSION['user_id'])) {
            view('fp-mail');
            exit;
        }
        header('Location: index.php?route=users/edit&id=' . $_SESSION['user_id']);
    }


    // data is coming from: POST:fp_mail
    public function sendForgotPasswordMail(array $data)
    {
        $email = $data['email'];

        if (empty($email)) {
            $errors['email'] = 'Email Address is required.';
            view('fp-mail', ['errors' => $errors]);
            exit;
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            $errors['email'] = 'User not found.';
            view('fp-mail', ['errors' => $errors]);
            exit;
        }

        if ($this->isSent($user['id'])) {
            $_SESSION['success'] = 'Mail already sent.';
            view('fp-mail');
            exit;
        }

        $this->sendResetPasswordMail($user['id']);
    }

    private function generateFPHash($user_id)
    {
        $code = bin2hex(random_bytes(32));
        $hash = password_hash($code, PASSWORD_DEFAULT);

        $expiresAt = (new DateTime())
            ->modify('+5 minutes')
            ->format('Y-m-d H:i:s');

        $stmt = $this->conn->prepare('insert into forgot_password (user_id, hash, expires_at) values (?, ?, ?)');
        $stmt->execute([$user_id, $hash, $expiresAt]);

        return [
            'id' => $this->conn->lastInsertId(),
            'code' => $code,
        ];
    }

    public function isSent($user_id)
    {
        $stmt = $this->conn->prepare('select count(*) from forgot_password where user_id = ? and expires_at > now()');
        $stmt->execute([$user_id]);

        return (int) $stmt->fetchColumn() > 0;
    }
}
