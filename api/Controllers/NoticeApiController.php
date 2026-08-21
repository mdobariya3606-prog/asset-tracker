<?php

declare(strict_types=1);

namespace Api\Controllers;

use Api\Response;
use App\Models\Notice;
use PDO;
use Throwable;

/** Handles notices, recipients, confirmation, updates, and deletion. */
final class NoticeApiController extends BaseApiController
{
    public function handle(?string $action, string $method, array $query, array $body): never
    {
        $this->requireAuth();
        $model = new Notice($this->conn);

        if ($action === 'mark-confirmed') {
            // Confirmation is limited to the logged-in user's recipient row.
            if ($method !== 'GET' && $method !== 'PATCH') Response::error('METHOD_NOT_ALLOWED', 'Use GET or PATCH to confirm a notice.', 405);
            $id = $this->id($query);
            $stmt = $this->conn->prepare('UPDATE notice_recipients SET confirmed_at = COALESCE(confirmed_at, NOW()) WHERE employee_id = ? AND notice_id = ?');
            $stmt->execute([(int)$_SESSION['user_id'], $id]);
            Response::send(['id' => $id, 'confirmed' => $stmt->rowCount() > 0]);
        }
        if ($action === 'show' || ($action === null && isset($query['id']))) {
            $notice = $model->find($this->id($query));
            $this->one($notice ? $notice : [], 'NOTICE_NOT_FOUND', 'Notice');
        }
        if ($method === 'GET') Response::send($model->all());
        if ($method === 'POST') {
            // A notice without an employee_id is broadcast to all users;
            // providing employee_id creates a personal notice.
            $titleId = filter_var($body['title_id'] ?? null, FILTER_VALIDATE_INT);
            $message = trim((string)($body['message'] ?? ''));
            $errors = [];
            if (!$titleId || $titleId < 1) $errors['title_id'] = 'Please select a valid notice title.';
            if (strlen($message) < 5) $errors['message'] = 'Message must be at least 5 characters.';
            if ($errors !== []) Response::error('VALIDATION_FAILED', 'Notice validation failed.', 422, $errors);
            $this->conn->beginTransaction();
            try {
                // Keep notice and recipient inserts atomic.
                $insert = $this->conn->prepare('INSERT INTO notices (title_id, message, created_by) VALUES (?, ?, ?)');
                $insert->execute([$titleId, $message, (int)$_SESSION['user_id']]);
                $noticeId = (int)$this->conn->lastInsertId();
                $ids = isset($body['employee_id']) ? [(int)$body['employee_id']] : $this->conn->query('SELECT id FROM users')->fetchAll(PDO::FETCH_COLUMN);
                $recipient = $this->conn->prepare('INSERT INTO notice_recipients (notice_id, employee_id) VALUES (?, ?)');
                foreach ($ids as $employeeId) $recipient->execute([$noticeId, (int)$employeeId]);
                $this->conn->commit();
                Response::send(['id' => $noticeId], 201);
            } catch (Throwable $exception) {
                if ($this->conn->inTransaction()) $this->conn->rollBack();
                throw $exception;
            }
        }
        if ($method === 'PATCH') {
            $id = $this->id($query);
            if (!$model->update($id, $body)) Response::error('NOTICE_NOT_FOUND', 'Notice not found.', 404);
            Response::send($model->find($id));
        }
        if ($method === 'DELETE') {
            $id = $this->id($query);
            if (!$model->delete($id)) Response::error('NOTICE_NOT_FOUND', 'Notice not found.', 404);
            Response::send(['id' => $id, 'deleted' => true]);
        }
        Response::error('METHOD_NOT_ALLOWED', 'Use GET, POST, PATCH, or DELETE for notices.', 405);
    }
}
