<?php

namespace App\Controllers\Notice;

use App\Models\Notice;
use PDO;

class SelectNoticeController
{
    private $conn;
    private Notice $notice;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
        $this->notice = new Notice($conn);
    }

    public function index()
    {
        middleware('auth');

        $notices = $this->notice->all();
        $this->notice->markSeen();

        view('notices.select', [
            'notices' => $notices
        ]);
        exit;
    }
}