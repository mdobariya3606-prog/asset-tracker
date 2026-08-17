<?php

use App\Config\Database;
use App\Models\AssetRequest;

function sendNotice($noticeId, $userId)
{
    $conn = (new Database())->getConnection();

    $stmt = $conn->prepare('insert into notice_recipients (notice_id, employee_id) values (?, ?)');
    $stmt->execute([$noticeId, $userId]);
}

function sendStatusNotice($status, array $assetRequest)
{
    $notices = getNotices();
    $status = strtoupper(trim($status));
    
    if (!in_array($status, $notices)) {
        return;
    }

    $noticeId = $notices[$status];
    if ($noticeId) {
        sendNotice($noticeId, $assetRequest['user_id']);
        echo $noticeId;
        exit;
    }
}

function getNotices()
{
    return [
        'REJECTED' => 11,
        'REMINDER' => 13,
        'OVERDUE' => 14
    ];
}
