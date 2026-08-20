<?php

namespace App\Controllers\Notice;

use App\helpers\Csrf;
use App\Models\Notice;
use PDO;
use Throwable;

class CreateNoticeController
{
    /* =========================================================
	 * PROPERTIES
	 * ========================================================= */

    private PDO $conn;
    private Notice $notice;

    /* =========================================================
	 * CONSTRUCTOR
	 * ========================================================= */

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
        $this->notice = new Notice($conn);
    }

    /* =========================================================
	 * CREATE NOTICE
	 * ========================================================= */

    public function create()
    {
        middleware('auth');
        middleware('hr');

        $noticeTitles = $this->notice->getTitles();

        view('notices.create', [
            'noticeTitles' => $noticeTitles,
        ]);

        exit;
    }

    /* =========================================================
	 * STORE NOTICE
	 * ========================================================= */

    public function store(array $postData)
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }

        $validation = $this->validateNotice($postData);

        if (!empty($validation['errors'])) {
            $noticeTitles = $this->notice->getTitles();

            view('notices.create', [
                'errors' => $validation['errors'],
                'old' => $validation['old'],
                'noticeTitles' => $noticeTitles,
            ]);

            exit;
        }

        $userId = $_SESSION['user_id'];

        $this->conn->beginTransaction();

        try {
            $stmt = $this->conn->prepare(
                'INSERT INTO notices
					(title_id, message, created_by)
				VALUES (?, ?, ?)'
            );

            $stmt->execute([
                $postData['title_id'],
                $postData['message'],
                $userId,
            ]);

            $noticeId = (int) $this->conn->lastInsertId();

            $stmt = $this->conn->query('SELECT id FROM users');
            $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $recipientStmt = $this->conn->prepare(
                'INSERT INTO notice_recipients
					(notice_id, employee_id)
				VALUES (?, ?)'
            );

            foreach ($userIds as $userId) {
                $recipientStmt->execute([
                    $noticeId,
                    $userId,
                ]);
            }

            $this->conn->commit();

            route('notices');
            exit;
        } catch (Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /* =========================================================
	 * VALIDATION
	 * ========================================================= */

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
            $errors['message'] =
                'Message must be at least 5 characters.';

            $old['message'] = $message;
        }

        if ($titleId == 0) {
            $errors['title_id'] =
                'Please select a notice title.';

            $old['message'] = $message;
        }

        if (empty($errors)) {
            $stmt = $this->conn->prepare(
                'SELECT id
				FROM notice_titles
				WHERE id = ?'
            );

            $stmt->execute([$titleId]);

            if ($stmt->rowCount() === 0) {
                $errors['title_id'] = 'Invalid title.';
                $old['message'] = $message;
            }
        }

        return [
            'errors' => $errors,
            'old' => $old,
        ];
    }
}
