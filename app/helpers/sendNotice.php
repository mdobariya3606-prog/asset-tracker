<?php

use App\Config\Database;

/**
 * Send an existing notice to one user.
 */
function sendNotice(int $noticeId, int $userId, ?\PDO $conn = null): void
{
    $conn = $conn ?: (new Database())->getConnection();

    $stmt = $conn->prepare('insert into notice_recipients (notice_id, employee_id) values (?, ?)');
    $stmt->execute([$noticeId, $userId]);
}

/**
 * Create and send a notice to a specific user.
 *
 * Notice titles are reused when possible so the notices page remains
 * manageable, while still allowing lifecycle notices to work on a fresh DB.
 */
function sendNoticeToUser(
    int $userId,
    string $title,
    string $message,
    ?\PDO $conn = null,
    ?int $createdBy = null
): int {
    $conn = $conn ?: (new Database())->getConnection();
    $createdBy = $createdBy ?: (int) ($_SESSION['user_id'] ?? 0);

    $titleStmt = $conn->prepare('SELECT id FROM notice_titles WHERE title = ? LIMIT 1');
    $titleStmt->execute([trim($title)]);
    $titleId = $titleStmt->fetchColumn();

    if (!$titleId) {
        $insertTitle = $conn->prepare('INSERT INTO notice_titles (title) VALUES (?)');
        $insertTitle->execute([trim($title)]);
        $titleId = (int) $conn->lastInsertId();
    }

    $noticeStmt = $conn->prepare(
        'INSERT INTO notices (title_id, message, created_by) VALUES (?, ?, ?)'
    );
    $noticeStmt->execute([(int) $titleId, trim($message), $createdBy]);
    $noticeId = (int) $conn->lastInsertId();

    sendNotice($noticeId, $userId, $conn);

    return $noticeId;
}

function sendStatusNotice(string $status, array $assetRequest, ?\PDO $conn = null): void
{
    $status = strtoupper(trim($status));

    $notice = getNotices()[$status] ?? null;
    if (!$notice) {
        return;
    }

    sendNoticeToUser(
        (int) $assetRequest['user_id'],
        $notice['title'],
        str_replace('{asset}', $assetRequest['asset_name'] ?? 'the requested asset', $notice['message']),
        $conn
    );
}

function getNotices()
{
    return [
        'APPROVED' => [
            'title' => 'Asset request approved',
            'message' => 'Your request for {asset} is approved. You can collect it.',
        ],
        'ISSUED' => [
            'title' => 'Asset issued',
            'message' => '{asset} has been issued to you.',
        ],
        'RETURNED' => [
            'title' => 'Asset returned',
            'message' => '{asset} has been returned successfully.',
        ],
        'REJECTED' => [
            'title' => 'Asset request rejected',
            'message' => 'Your request for {asset} was rejected.',
        ],
    ];
}
