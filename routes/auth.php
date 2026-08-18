<?php

use App\Controllers\Email\ForgotPasswordEmail;
use App\Controllers\Email\ResetPasswordController as EmailResetPasswordController;
use App\Controllers\User\LoginController;
use App\Controllers\Email\ResetPasswordController as ResetViaEmail;

switch ("$method:$route") {
    case 'GET:login':
        (new LoginController($conn))->showLoginForm();
        return true;

    case 'POST:login':
        (new LoginController($conn))->login($_POST);
        return true;

    case 'POST:logout':
        (new LoginController($conn))->signout();
        return true;

    case 'GET:send-rp-mail':
        (new ForgotPasswordEmail($conn))->sendResetPasswordMail();
        return true;

    case 'GET:reset-password':
        (new EmailResetPasswordController($conn))->resetPassword($_GET);
        return true;

    case 'GET:fp-mail':
        view('fp-mail');
        return true;

    case 'POST:fp-mail':
        (new ForgotPasswordEmail($conn))->sendForgotPasswordMail($_POST);
        return true;

    case 'POST:reset-password':
        (new ResetViaEmail($conn))->updatePassword($_GET, $_POST);
        return true;

    case 'GET:force-error':
        echo 1 / 0;
        return true;
}

return false;