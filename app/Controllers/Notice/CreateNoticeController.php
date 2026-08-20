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

    public function customCreate(): void
    {
        middleware('auth');
        $this->authorizeCustomNotice();

        view('notices.create-custom', [
            'employees' => $this->recipientUsers(),
            'assets' => $this->assets(),
            'noticeTitles' => $this->notice->getTitles(),
        ]);
        exit;
    }

    public function customStore(array $postData): void
    {
        middleware('auth');
        $this->authorizeCustomNotice();

        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            view(403);
            exit;
        }

        $validation = $this->validateCustomNotice($postData);
        if (!empty($validation['errors'])) {
            view('notices.create-custom', [
                'errors' => $validation['errors'],
                'old' => $validation['old'],
                'employees' => $this->recipientUsers(),
                'assets' => $this->assets(),
                'noticeTitles' => $this->notice->getTitles(),
            ]);
            exit;
        }

        $recipientId = (int) $postData['employee_id'];
        $assetId = !empty($postData['asset_id']) ? (int) $postData['asset_id'] : null;
        $message = trim($postData['message']);

        if ($assetId !== null) {
            $asset = $this->findAsset($assetId);
            $message .= "\n\nRelated asset: {$asset['name']}" .
                (!empty($asset['serial_number']) ? " (S/N: {$asset['serial_number']})" : '');
        }

        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare(
                'INSERT INTO notices (title_id, message, created_by) VALUES (?, ?, ?)'
            );
            $stmt->execute([
                (int) $postData['title_id'],
                $message,
                (int) $_SESSION['user_id'],
            ]);

            $noticeId = (int) $this->conn->lastInsertId();
            $recipientStmt = $this->conn->prepare(
                'INSERT INTO notice_recipients (notice_id, employee_id) VALUES (?, ?)'
            );
            $recipientStmt->execute([$noticeId, $recipientId]);

            $this->conn->commit();
            $_SESSION['success'] = 'Personal notice sent successfully.';
            route('notices');
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    /* =========================================================
	 * STORE NOTICE
	 * ========================================================= */

    public function store(array $postData)
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            view(403);
            exit;
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

    private function authorizeCustomNotice(): void
    {
        $role = strtoupper((string) ($_SESSION['user_role'] ?? ''));
        if (!in_array($role, ['ADMIN', 'HR', 'MANAGER'], true)) {
            view(403);
            exit;
        }
    }

    private function recipientUsers(): array
    {
        $sql = 'SELECT id, name, email, department_id
                FROM users
                WHERE id != ? AND deleted_at IS NULL
                ORDER BY name';

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function assets(): array
    {
        return $this->conn
            ->query('SELECT id, name, serial_number FROM assets ORDER BY name')
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    private function findAsset(int $id): array
    {
        $stmt = $this->conn->prepare('SELECT id, name, serial_number FROM assets WHERE id = ?');
        $stmt->execute([$id]);
        $asset = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$asset) {
            throw new \RuntimeException('Selected asset was not found.');
        }
        return $asset;
    }

    private function validateCustomNotice(array $notice): array
    {
        $errors = [];
        $old = [
            'employee_id' => (int) ($notice['employee_id'] ?? 0),
            'asset_id' => (int) ($notice['asset_id'] ?? 0),
            'title_id' => (int) ($notice['title_id'] ?? 0),
            'message' => trim((string) ($notice['message'] ?? '')),
        ];

        $allowedEmployeeIds = array_map(
            'intval',
            array_column($this->recipientUsers(), 'id')
        );

        if (!in_array($old['employee_id'], $allowedEmployeeIds, true)) {
            $errors['employee_id'] = 'Please select a valid employee.';
        }

        $titleStmt = $this->conn->prepare('SELECT id FROM notice_titles WHERE id = ?');
        $titleStmt->execute([$old['title_id']]);
        if (!$titleStmt->fetchColumn()) {
            $errors['title_id'] = 'Please select a valid notice title.';
        }

        if ($old['message'] === '') {
            $errors['message'] = 'Message is required.';
        } elseif (strlen($old['message']) < 5) {
            $errors['message'] = 'Message must be at least 5 characters.';
        }

        if ($old['asset_id'] > 0) {
            $assetStmt = $this->conn->prepare('SELECT id FROM assets WHERE id = ?');
            $assetStmt->execute([$old['asset_id']]);
            if (!$assetStmt->fetchColumn()) {
                $errors['asset_id'] = 'Please select a valid asset.';
            }
        } else {
            $old['asset_id'] = 0;
        }

        return ['errors' => $errors, 'old' => $old];
    }
}
