<?php

namespace App\Controllers\Notice;

use App\helpers\Csrf;
use App\Models\Notice;
use PDO;

class EditNoticeController
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
	 * EDIT NOTICE
	 * ========================================================= */

    public function edit(array $getParams)
    {
        middleware('auth');
        middleware('admin');

        $id = (int) ($getParams['id'] ?? 0);
        $notice = $this->notice->find($id);

        if (!$notice) {
            view(404);
            exit;
        }

        $noticeTitles = $this->notice->getTitles();

        view('notices.edit', [
            'notice' => $notice,
            'personalRecipient' => $this->notice->personalRecipient($id),
            'noticeTitles' => $noticeTitles,
            'errors' => [],
            'old' => [],
        ]);

        exit;
    }

    /* =========================================================
	 * UPDATE NOTICE
	 * ========================================================= */

    public function update(array $getParams, array $postData)
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            view(403);
            exit;
        }

        middleware('auth');
        middleware('admin');

        $id = (int) ($getParams['id'] ?? 0);
        $notice = $this->notice->find($id);

        if (!$notice) {
            view(404);
            exit;
        }

        $validation = $this->validateNotice($postData);

        if (!empty($validation['errors'])) {
            $noticeTitles = $this->notice->getTitles();

            view('notices.edit', [
                'notice' => $notice,
                'personalRecipient' => $this->notice->personalRecipient($id),
                'noticeTitles' => $noticeTitles,
                'errors' => $validation['errors'],
                'old' => $validation['old'],
            ]);

            exit;
        }

        $this->notice->update($id, $postData);

        $_SESSION['success'] = 'Notice updated successfully.';
        route('notices');
        exit;
    }

    /* =========================================================
	 * DELETE NOTICE
	 * ========================================================= */

    public function destroy(array $getParams)
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            view(403);
            exit;
        }

        middleware('auth');
        middleware('admin');

        $id = (int) ($getParams['id'] ?? 0);
        $notice = $this->notice->find($id);

        if (!$notice) {
            view(404);
            exit;
        }

        $this->notice->delete($id);

        $_SESSION['success'] = 'Notice deleted successfully.';
        route('notices');
        exit;
    }

    /* =========================================================
	 * VALIDATION
	 * ========================================================= */

    private function validateNotice(array $notice): array
    {
        $titleId = (int) ($notice['title_id'] ?? 0);
        $message = trim($notice['message'] ?? '');

        $errors = [];
        $old = [
            'title_id' => $titleId,
            'message' => $message,
        ];

        if (empty($message)) {
            $errors['message'] = 'Message is required.';
        } elseif (strlen($message) < 5) {
            $errors['message'] =
                'Message must be at least 5 characters.';
        }

        if ($titleId === 0) {
            $errors['title_id'] =
                'Please select a notice title.';
        } else {
            $stmt = $this->conn->prepare(
                'SELECT id
				FROM notice_titles
				WHERE id = ?'
            );

            $stmt->execute([$titleId]);

            if ($stmt->rowCount() === 0) {
                $errors['title_id'] =
                    'Invalid notice title.';
            }
        }

        return [
            'errors' => $errors,
            'old' => $old,
        ];
    }
}
