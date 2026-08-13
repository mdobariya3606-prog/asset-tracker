<?php

function middleware(string $middleware): void
{
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

    require $requiredMiddleware;
}
