<?php

$stmt = $conn->prepare('SELECT * from users where id = ?');
if (!$stmt->execute([$_SESSION['user_id']])) {
    view(404);
    exit;
}

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user['deleted_at']) {
    view(404);
    exit;
}