<?php

require_once 'auth.php';
if ($_SESSION['user_role'] !== 'HR'
|| $_SESSION['user_role'] !== 'ADMIN') {
    view(403);
    exit;
}