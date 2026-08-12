<?php

use App\Config\Database;
use App\Models\Asset;
use App\Models\AssetRequest;

require_once 'auth.php';

$assetRequest = (new AssetRequest((new Database())->getConnection()))->findOrFail($_GET['id']);

if ($assetRequest['user_id'] != $_SESSION['user_id'] 
&& $_SESSION['user_role'] !== 'ADMIN' 
&& $_SESSION['user_role'] !== 'MANAGER') {
    view(403);
    exit;
}