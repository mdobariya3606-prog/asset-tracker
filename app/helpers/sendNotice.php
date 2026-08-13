<?php 

use App\Config\Database;

function sendNotice($noticeId, $userId) {
    $conn = (new Database())->getConnection();
    /**
     * @var PDO $conn
     */
    $stmt = $conn->prepare('insert into notice_recipients (notice_id, employee_id) values (?, ?)');
    $stmt->execute([$noticeId, $userId]);
}