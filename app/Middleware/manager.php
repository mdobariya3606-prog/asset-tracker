<?php

require_once 'auth.php';

if ($_SESSION['user_role'] !== 'MANAGER'
&& $_SESSION['user_role'] !== 'ADMIN') {
    view(403);
    exit;
}