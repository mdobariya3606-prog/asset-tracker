<?php

namespace App\Controllers\Email;

use App\Models\ForgotPassword;
use App\Models\User;
use App\Services\ResetPasswordService;
use App\Validators\ResetPasswordValidator;
use PDO;
use RuntimeException;

class ResetPasswordController
{
    private ResetPasswordService $resetPasswordService;
    private ResetPasswordValidator $validator;

    public function __construct(PDO $conn)
    {
        $userModel = new User($conn);
        $forgotPasswordModel = new ForgotPassword($conn);

        $this->resetPasswordService = new ResetPasswordService(
            $userModel,
            $forgotPasswordModel
        );

        $this->validator = new ResetPasswordValidator();
    }

    public function resetPassword(array $data)
    {
        $id = (int) $data['id'];
        $code = $data['code'];

        try {
            $this->resetPasswordService->verifyRequest($id, $code);
        } catch (RuntimeException $e) {
            view(404, [
                'message' => $e->getMessage(),
            ]);
            exit;
        }

        view('reset-password');
        exit;
    }

    public function updatePassword(
        array $getData,
        array $postData
    ) {
        $id = (int) $getData['id'];
        $code = $getData['code'];

        try {
            $reset = $this->resetPasswordService->verifyRequest(
                $id,
                $code
            );
        } catch (RuntimeException $e) {
            view(404, [
                'message' => $e->getMessage(),
            ]);
            exit;
        }

        $password = $postData['password'] ?? '';
        $confirmPassword = $postData['confirm_password'] ?? '';

        $errors = $this->validator->validate(
            $password,
            $confirmPassword
        );

        if (!empty($errors)) {
            view('reset-password', [
                'errors' => $errors,
            ]);
            exit;
        }

        try {
            $this->resetPasswordService->resetPassword(
                $reset['user_id'],
                $password
            );
        } catch (RuntimeException $e) {
            view(404, [
                'message' => $e->getMessage(),
            ]);
            exit;
        }

        $_SESSION['success'] = 'Password updated successfully.';

        route(
            $_SESSION['user_id']
                ? 'users'
                : 'login'
        );

        exit;
    }
}
