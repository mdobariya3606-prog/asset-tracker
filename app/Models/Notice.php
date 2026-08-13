<?php

namespace App\Models;

use PDO;

class Notice
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function all(): array
    {
        $stmt = $this->conn->prepare('
        select n.*, 
        nt.title as title,
        nr.id as nr_id,
        nr.confirmed_at
        from notices n

        join notice_titles nt on n.title_id = nt.id
        left join notice_recipients nr on n.id = nr.notice_id

        where employee_id = ?
        order by nr.created_at desc, nr.confirmed_at, n.id');

        $stmt->execute([$_SESSION['user_id']]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTitles()
    {
        $stmt = $this->conn->query('select id, title from notice_titles');

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function pendingNotices()
    {
        $stmt = $this->conn->prepare('select count(*) as pending_requests from notice_recipients where employee_id = ? and confirmed_at is null');

        $stmt->execute([$_SESSION['user_id']]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['pending_requests'];
    }

    public function markSeen()
    {
        $stmt = $this->conn->prepare(
            "update notice_recipients set last_seen = now() where employee_id = ?"
        );

        $stmt->execute([$_SESSION['user_id']]);
    }

    public function markConfirmed()
    {

        if (empty($_GET['id']) || !isset($_GET['id'])) {
            view(404);
            exit;
        }

        $noticeId = $_GET['id'];

        $stmt = $this->conn->prepare(
            "update notice_recipients 
            set confirmed_at = COALESCE(confirmed_at, now())
            where employee_id = ? and notice_id = ?"
        );
        $stmt->execute([$_SESSION['user_id'], $noticeId]);

        route('notices');
    }
}
