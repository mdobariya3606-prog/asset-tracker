<?php

use App\Config\Database;

function middleware(string $middleware, array $vars = []): void
{
    $conn = (new Database())->getConnection();
    
    $prefix = __DIR__ . '/../Middleware';

    $middlewares = [
        'admin' => $prefix . '/admin.php',
        'asset' => $prefix . '/asset.php',
        'assetOwner' => $prefix . '/assetOwner.php',
        'auth' => $prefix . '/auth.php',
        'hr' => $prefix . '/hr.php',
        'manager' => $prefix . '/manager.php',
    ];

    $requiredMiddleware = $middlewares[$middleware] ?? null;

    if (!$requiredMiddleware) {
        view(403);
        exit;
    }
    
    // extract($vars, 1);
    extract($vars, EXTR_SKIP);

    require_once $requiredMiddleware;
}
