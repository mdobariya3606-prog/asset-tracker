<?php

function view(int|string $viewFile, array $vars = []): void
{
    $prefix = __DIR__ . '/../../resources/views';

    $pages = [
        'header' => __DIR__ . '/../../resources/views/layouts/header.php',
        'login' => $prefix . '/users/login.php',

        403 => $prefix . '/errors/403.php',
        404 => $prefix . '/errors/404.php',
        500 => $prefix . '/errors/500.php',

        'users.edit' => $prefix . '/users/edit.php',
        'users.profile' => $prefix . '/users/profile.php',
        'users.register' => $prefix . '/users/register.php',
        'users.reset-password' => $prefix . '/users/reset-password.php',
        'users.select' => $prefix . '/users/select.php',
        'users.pdf' => $prefix . '/users/export-pdf.php',
        'users.excel' => $prefix . '/users/export-excel.php',
        
        'notices.select' => $prefix . '/notices/select.php',
        'notices.create' => $prefix . '/notices/create.php',
        
        'departments.create' => $prefix . '/departments/create.php',
        'departments.select' => $prefix . '/departments/select.php',

        'designations.create' => $prefix . '/designation/create.php',
        'designations.select' => $prefix . '/designation/select.php',

        'assets.create' => $prefix . '/assets/create.php',
        'assets.edit' => $prefix . '/assets/edit.php',
        'assets.select' => $prefix . '/assets/select.php',
        'assets.show' => $prefix . '/assets/show.php',
        'assets.pdf' => $prefix . '/assets/export-pdf.php',
        'assets.excel' => $prefix . '/assets/export-excel.php',

        'asset.requests.create' => $prefix . '/asset_requests/create.php',
        'asset.requests.select' => $prefix . '/asset_requests/select.php',
        'asset.requests.manage' => $prefix . '/asset_requests/manage.php',
        'asset.requests.show' => $prefix . '/asset_requests/show.php',
        'asset.requests.pdf' => $prefix . '/asset_requests/export-pdf.php',
        'asset.requests.excel' => $prefix . '/asset_requests/export-excel.php',

        'reset-password' => $prefix . '/auth/reset_password.php',
        'fp-mail' => $prefix . '/auth/fp_mail.php',
    ];

    $viewFile = $pages[$viewFile] ?? null;

    if (!$viewFile) {
        view(404);
        exit;
    }

    extract($vars, EXTR_SKIP);
    require $viewFile;
}
