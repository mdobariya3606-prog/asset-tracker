<?php

namespace App\Controllers\Email;

use App\helpers\Csrf;
use App\Models\AuditLog;
use App\Models\ForgotPassword;
use App\Models\User;
use App\Services\ResetPasswordService;
use App\Validators\ResetPasswordValidator;
use PDO;
use RuntimeException;

class ResetPasswordController
{
    /* =========================================================
	 * PROPERTIES
	 * ========================================================= */

    private PDO $conn;
    private ResetPasswordService $resetPasswordService;
    private ResetPasswordValidator $validator;

    /* =========================================================
	 * CONSTRUCTOR
	 * ========================================================= */

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;

        $userModel = new User($conn);
        $forgotPasswordModel = new ForgotPassword($conn);

        $this->resetPasswordService = new ResetPasswordService(
            $userModel,
            $forgotPasswordModel
        );

        $this->validator = new ResetPasswordValidator();
    }

    /* =========================================================
	 * RESET PASSWORD
	 * ========================================================= */

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

    /* =========================================================
	 * UPDATE PASSWORD
	 * ========================================================= */

    public function updatePassword(
        array $getData,
        array $postData
    ) {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }

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

        $this->resetPasswordService->removeToken($id);

        $_SESSION['success'] = 'Password updated successfully.';

        (new AuditLog($this->conn))->log(
            'PASSWORD_CHANGE',
            null,
            $reset['user_id']
        );

        route(
            $_SESSION['user_id']
                ? 'users'
                : 'login'
        );

        exit;
    }
}
