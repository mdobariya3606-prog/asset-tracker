<?php

namespace App\Validators;

class ResetPasswordValidator
{
    public function validate(
        string $password,
        string $confirmPassword
    ): array {
        $errors = [];

        if (empty($password)) {
            $errors['password'] = 'New Password is required.';
        } elseif (empty($confirmPassword)) {
            $errors['confirm_password'] =
                'Please confirm your password.';
        } elseif (strlen($password) < 6) {
            $errors['password'] =
                'Password must be at least 6 characters long.';
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] =
                'Passwords do not match.';
        }

        return $errors;
    }
}