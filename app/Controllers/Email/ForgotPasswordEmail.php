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
                "https://asset-tracker.page.gd/index.php" .
                "?route=reset-password" .
                "&id={$generatedCode['id']}" .
                "&code=" . urlencode($generatedCode['code']);

            $mailAddress = $user[0]['email'];

            if (empty($mailAddress) || !$this->mail->send(
                $mailAddress,
                'Forgot password',
                $this->renderResetPasswordEmail(
                    $user[0]['name'] ?? 'there',
                    $mailAddress,
                    $link
                )
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

    private function renderResetPasswordEmail(
        string $name,
        string $email,
        string $link
    ): string {
        $safeName = htmlspecialchars(
            trim($name) !== '' ? trim($name) : 'there',
            ENT_QUOTES,
            'UTF-8'
        );
        $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $safeLink = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset your Asset Tracker password</title>
</head>
<body style="margin:0;padding:0;background:#f3f7fb;color:#172033;font-family:Arial,Helvetica,sans-serif;">
    <div style="padding:36px 16px;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
            style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 28px rgba(31,50,81,.10);">
            <tr>
                <td style="padding:28px 36px;background:#183b56;color:#ffffff;">
                    <div style="font-size:13px;letter-spacing:1.6px;text-transform:uppercase;opacity:.82;">Asset Tracker</div>
                    <h1 style="margin:14px 0 0;font-size:28px;line-height:1.25;">Reset your password</h1>
                </td>
            </tr>
            <tr>
                <td style="padding:36px;">
                    <p style="margin:0 0 18px;font-size:18px;line-height:1.5;">Hi {$safeName},</p>
                    <p style="margin:0 0 22px;color:#526174;font-size:15px;line-height:1.7;">
                        We received a request to reset the password for
                        <strong style="color:#172033;">{$safeEmail}</strong>.
                        Click the button below to choose a new password.
                    </p>
                    <table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 auto 26px;">
                        <tr>
                            <td style="border-radius:8px;background:#147d92;text-align:center;">
                                <a href="{$safeLink}" style="display:inline-block;padding:14px 24px;border-radius:8px;color:#ffffff;font-size:15px;font-weight:bold;text-decoration:none;">Reset password</a>
                            </td>
                        </tr>
                    </table>
                    <p style="margin:0 0 18px;color:#718096;font-size:13px;line-height:1.6;">
                        This link expires in 5 minutes and can only be used once.
                        If you did not request a password reset, you can safely ignore this email.
                    </p>
                    <p style="margin:0;color:#9aa6b5;font-size:12px;line-height:1.6;word-break:break-all;">
                        If the button does not work, copy and paste this link into your browser:<br>
                        <a href="{$safeLink}" style="color:#147d92;">{$safeLink}</a>
                    </p>
                </td>
            </tr>
            <tr>
                <td style="padding:20px 36px;background:#f8fafc;color:#8793a5;font-size:12px;line-height:1.5;text-align:center;">
                    This is an automated message from Asset Tracker. Please do not reply to this email.
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
HTML;
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
