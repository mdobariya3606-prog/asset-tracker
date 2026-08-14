<?php

$method = $_SERVER['REQUEST_METHOD'];
$route = $_GET['route'] ?? '';

try {
    $routeFiles = [
        'home.php',
        'departments.php',
        'designations.php',
        'notices.php',
        'users.php',
        'assets.php',
        'asset_requests.php',
        'exports.php',
        'auth.php',
    ];

    $handled = false;

    foreach ($routeFiles as $routeFile) {
        $handled = require __DIR__ . '/' . $routeFile;

        if ($handled) {
            break;
        }
    }

    if (!$handled) {
        view(404);
        exit;
    }
} catch (Throwable $e) {
    logError($e);
    view(500);
    exit;
}
