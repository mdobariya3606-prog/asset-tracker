<?php

use App\Controllers\User\CreateUserController;
use App\Controllers\User\EditUserController;
use App\Controllers\User\ProfileController;
use App\Controllers\User\ResetPasswordController;
use App\Controllers\User\SelectUserController;
use App\Models\Route;

switch ("$method:$route") {
    case 'GET:users':
        (new SelectUserController($conn))->index($_GET);
        return true;

    case 'GET:users/deleted':
        (new SelectUserController($conn))->trash();
        return true;

    case 'GET:users/create':
        (new CreateUserController($conn))->create();
        return true;

    case 'POST:users/create':
        (new CreateUserController($conn))->store($_POST);
        return true;

    case 'GET:users/edit':
        (new EditUserController($conn))->edit($_GET);
        return true;

    case 'POST:users/edit':
        (new EditUserController($conn))->updateUser($_GET, $_POST);
        return true;

    case 'GET:users/profile':
        (new ProfileController($conn))->show($_GET);
        return true;

    case 'GET:users/reset-password':
        (new ResetPasswordController($conn))->edit($_GET);
        return true;

    case 'POST:users/reset-password':
        (new ResetPasswordController($conn))->store($_GET, $_POST);
        return true;

    case 'GET:users/delete':
        (new EditUserController($conn))->destroy($_GET);
        return true;

    case 'GET:chart':
        require_once __DIR__ . '/../public/testing/chart.php';
        exit;
}

return false;