<?php

namespace App\Config;

use PHPMailer\PHPMailer\PHPMailer;
use Throwable;

class Mail
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        $this->configure();
    }

    private function configure()
    {
        $this->mailer->isSMTP();

        $this->mailer->Host = $_ENV['MAIL_HOST'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $_ENV['MAIL_USERNAME'];
        $this->mailer->Password = $_ENV['MAIL_PASSWORD'];

        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $_ENV['MAIL_PORT'];

        $this->mailer->setFrom(
            $_ENV['MAIL_FROM_ADDRESS'],
            $_ENV['MAIL_FROM_NAME']
        );

        $this->mailer->isHTML(true);
    }

    public function send(
        string $to,
        string $subject,
        string $body
    ): bool {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;

            return (bool) $this->mailer->send();
        } catch (Throwable $e) {
            // Email is a secondary action. Do not break the primary request.
            logError($e, 'mail');
            return false;
        }
    }
}
