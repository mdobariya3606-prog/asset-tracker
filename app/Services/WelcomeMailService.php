<?php

namespace App\Services;

use App\Config\Mail;

class WelcomeMailService
{
	private Mail $mail;

	public function __construct()
	{
		$this->mail = new Mail();
	}

	public function send(array $user): void
	{
		$email = trim((string) ($user['email'] ?? ''));

		if ($email === '') {
			return;
		}

		$name = htmlspecialchars(
			trim((string) ($user['name'] ?? 'there')),
			ENT_QUOTES,
			'UTF-8'
		);
		$role = htmlspecialchars(
			ucfirst(strtolower((string) ($user['role'] ?? 'employee'))),
			ENT_QUOTES,
			'UTF-8'
		);
		$safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
		$loginUrl = htmlspecialchars(
			rtrim((string) ($_ENV['APP_URL'] ?? ''), '/') . '/index.php?route=login',
			ENT_QUOTES,
			'UTF-8'
		);

		$body = $this->render($name, $safeEmail, $role, $loginUrl);

		$this->mail->send(
			$email,
			'Welcome to Asset Tracker',
			$body
		);
	}

	private function render(
		string $name,
		string $email,
		string $role,
		string $loginUrl
	): string {
		return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Asset Tracker</title>
</head>
<body style="margin:0; padding:0; background:#f4f7fb; color:#172033; font-family:Arial, Helvetica, sans-serif;">
    <div style="padding:40px 16px;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 8px 30px rgba(31, 50, 81, 0.10);">
            <tr>
                <td style="padding:32px 40px; background:#183b56; color:#ffffff;">
                    <div style="font-size:14px; letter-spacing:1.5px; text-transform:uppercase; opacity:.8;">Asset Tracker</div>
                    <h1 style="margin:16px 0 0; font-size:30px; line-height:1.2;">Welcome aboard!</h1>
                </td>
            </tr>
            <tr>
                <td style="padding:40px;">
                    <p style="margin:0 0 20px; font-size:18px; line-height:1.5;">Hi {$name},</p>
                    <p style="margin:0 0 24px; color:#526174; font-size:16px; line-height:1.7;">
                        Your Asset Tracker account has been created successfully. You can now sign in to request and manage company assets.
                    </p>
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 28px; background:#f4f8fc; border:1px solid #e2eaf2; border-radius:10px;">
                        <tr>
                            <td style="padding:18px 20px; color:#526174; font-size:14px; line-height:1.8;">
                                <strong style="color:#172033;">Account email:</strong> {$email}<br>
                                <strong style="color:#172033;">Assigned role:</strong> {$role}
                            </td>
                        </tr>
                    </table>
                    <a href="{$loginUrl}" style="display:inline-block; padding:14px 24px; background:#147d92; border-radius:8px; color:#ffffff; font-size:15px; font-weight:bold; text-decoration:none;">Sign in to Asset Tracker</a>
                    <p style="margin:28px 0 0; color:#718096; font-size:13px; line-height:1.6;">
                        For your security, your password is not included in this email. If you did not expect this account, please contact your administrator.
                    </p>
                </td>
            </tr>
            <tr>
                <td style="padding:22px 40px; background:#f8fafc; color:#8793a5; font-size:12px; line-height:1.5; text-align:center;">
                    This is an automated message from Asset Tracker. Please do not reply to this email.
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
HTML;
	}
}
