<?php 

if ($assetRequest['user_id'] !== $_SESSION['user_id']) {
    view(403);
    exit;
}