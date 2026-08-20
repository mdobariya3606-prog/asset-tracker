<?php

namespace App\Controllers\Email;

use App\Config\Mail;
use App\helpers\Csrf;
use App\Models\User;
use DateTime;
use PDO;
use RuntimeException;
use Throwable;

class ForgotPasswordEmail
{
    /* =========================================================
	 * PROPERTIES
	 * ========================================================= */

    private PDO $conn;
    private User $userModel;
    private Mail $mail;

    /* =========================================================
	 * CONSTRUCTOR
	 * ========================================================= */

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
        $this->userModel = new User($conn);
        $this->mail = new Mail();
    }

    /* =========================================================
	 * SEND RESET PASSWORD MAIL
	 * ========================================================= */

    public function sendResetPasswordMail($user_id = null)
    {
        if (!isset($_SESSION['user_id']) && !$user_id) {
            route('fp-mail');
            exit;
        }

        $user_id = $user_id ?? $_SESSION['user_id'];

        if ($this->isSent($user_id)) {
            $_SESSION['success'] = 'Mail already sent.';

            header(
                'Location: index.php?route=users/edit&id=' .
                    $_SESSION['user_id']
            );

            exit;
        }

        $user = $this->userModel->find($user_id);

        if (!$user) {
            view(404);
            exit;
        }

        $this->conn->beginTransaction();

        try {
            $generatedCode = $this->generateFPHash($user_id);

            $link =
                "http://localhost/AssetTracker/index.php" .
                "?route=reset-password" .
                "&id={$generatedCode['id']}" .
                "&code=" . urlencode($generatedCode['code']);

            $mailAddress = $user[0]['email'];

            if (empty($mailAddress) || !$this->mail->send(
                $mailAddress,
                'Forgot password',
                $link
            )) {
                throw new RuntimeException('Password reset email could not be sent.');
            }

            $this->conn->commit();
            $_SESSION['success'] = 'Mail sent successfully.';
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            logError($e, 'mail');
            $message = 'The email could not be sent. No reset request was saved. Please try again later.';

            if (empty($_SESSION['user_id'])) {
                view('fp-mail', [
                    'errors' => ['email' => $message],
                ]);
                exit;
            }

            $_SESSION['error'] = $message;
            header(
                'Location: index.php?route=users/edit&id=' .
                    $_SESSION['user_id']
            );
            exit;
        }

        if (empty($_SESSION['user_id'])) {
            view('fp-mail');
            exit;
        }

        header(
            'Location: index.php?route=users/edit&id=' .
                $_SESSION['user_id']
        );
    }

    /* =========================================================
	 * SEND FORGOT PASSWORD MAIL
	 * ========================================================= */

    public function sendForgotPasswordMail(array $data)
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            view(403);
            exit;
        }

        $email = $data['email'];

        if (empty($email)) {
            $errors['email'] = 'Email Address is required.';

            view('fp-mail', [
                'errors' => $errors,
            ]);

            exit;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            $errors['email'] = 'Mail already sent.';

            view('fp-mail', [
                'errors' => $errors,
            ]);

            exit;
        }

        if ($this->isSent($user['id'])) {
            $_SESSION['success'] = 'Mail already sent.';

            view('fp-mail');
            exit;
        }

        $this->sendResetPasswordMail($user['id']);
    }

    /* =========================================================
	 * RESET CODE
	 * ========================================================= */

    private function generateFPHash($user_id)
    {
        $code = bin2hex(random_bytes(32));
        $hash = password_hash($code, PASSWORD_DEFAULT);

        $expiresAt = (new DateTime())
            ->modify('+5 minutes')
            ->format('Y-m-d H:i:s');

        $stmt = $this->conn->prepare(
            'insert into forgot_password
				(user_id, hash, expires_at)
			values (?, ?, ?)'
        );

        $stmt->execute([
            $user_id,
            $hash,
            $expiresAt,
        ]);

        return [
            'id' => $this->conn->lastInsertId(),
            'code' => $code,
        ];
    }

    /* =========================================================
	 * CHECK RESET MAIL STATUS
	 * ========================================================= */

    public function isSent($user_id)
    {
        $stmt = $this->conn->prepare(
            'select count(*)
			from forgot_password
			where user_id = ?
				and expires_at > now()'
        );

        $stmt->execute([$user_id]);

        return (int) $stmt->fetchColumn() > 0;
    }
}
