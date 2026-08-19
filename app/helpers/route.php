<?php

function route(string $route, $params = ''): void
{
    $routes = [
        'login',
        'assets',
        'assets/history',
        'assets/requests',
        'assets/requests/cancel',
        'departments',
        'designations',
        'users',
        'users/create',
        'fp-mail',
        'notices',
    ];

    if (!in_array($route, $routes, true)) {
        view(404);
        exit;
    }

    header("Location: index.php?route=$route&" . $params);
    exit;
}
