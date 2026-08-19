<?php

namespace App\Controllers\Notice;

use App\helpers\Csrf;
use App\Models\Notice;
use PDO;
use Throwable;

class CreateNoticeController
{
    private PDO $conn;
    private Notice $notice;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
        $this->notice = new Notice($conn);
    }

    public function create()
    {
        middleware('auth');
        middleware('hr');

        $noticeTitles = $this->notice->getTitles();

        view('notices.create', ['noticeTitles' => $noticeTitles]);
        exit;
    }

    public function store(array $postData)
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }

        $errors = $this->validateNotice($postData);

        if (!empty($errors['errors'])) {
            $noticeTitles = $this->notice->getTitles();
            view('notices.create', [
                'errors' => $errors['errors'],
                'old' => $errors['old'],
                'noticeTitles' => $noticeTitles,
            ]);
            exit;
        }

        $user_id = $_SESSION['user_id'];

        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare(
                'insert into notices (title_id, message, created_by) values (?, ?, ?)'
            );

            $stmt->execute([
                $postData['title_id'],
                $postData['message'],
                $user_id
            ]);

            $noticeId = (int) $this->conn->lastInsertId();

            $stmt = $this->conn->query('select id from users');
            $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $recipientStmt = $this->conn->prepare('insert into notice_recipients (notice_id, employee_id) values (?, ?)');

            foreach ($userIds as $userId) {
                $recipientStmt->execute([$noticeId, $userId]);
            }

            $this->conn->commit();

            route('notices');
            exit;
        } catch (Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    private function validateNotice($notice)
    {
        $titleId = $notice['title_id'];
        $message = trim($notice['message']);
        $errors = [];
        $old = [];

        if (empty($message)) {
            $errors['message'] = 'Message is required.';
        }

        if (!empty($message) && strlen($message) < 5) {
            $errors['message'] = 'Message must be at least 5 characters.';
            $old['message'] = $message;
        }

        if ($titleId == 0) {
            $errors['title_id'] = 'Please select a notice title.';
            $old['message'] = $message;
        }

        if (empty($errors)) {
            $stmt = $this->conn->prepare('select id from notice_titles where id = ?');
            $stmt->execute([$titleId]);

            if ($stmt->rowCount() === 0) {
                $errors['title_id'] = "Invalid title.";
                $old['message'] = $message;
            }
        }

        return [
            'errors' => $errors,
            'old' => $old
        ];
    }
}
