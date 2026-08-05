<?php

namespace App\Controllers\User;

use App\Models\User;
use PDO;

class EditUserController
{
    private PDO $conn;
    private User $user;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
        $this->user = new User($conn);
    }

    /**
     * Retrieve data for editing a user.
     *
     * @param int $id
     * @return array|null
     */
    public function showForm(int $id): ?array
    {
        $userData = $this->user->find($id);
        if (empty($userData)) {
            return null;
        }

        return [
            'user'         => $userData[0],
            'departments'  => $this->getDepartments(),
            'designations' => $this->getDesignations(),
        ];
    }

    /**
     * Update a user profile.
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update(int $id, array $data): array
    {
        $errors = $this->user->validate($data, true, $id);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors'  => $errors,
                'old'     => $data,
            ];
        }

        $success = $this->user->update($id, $data);

        return [
            'success' => $success,
            'errors'  => $success ? [] : ['general' => 'Failed to update user in the database.'],
        ];
    }

    /**
     * Delete a user by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->user->delete($id);
    }

    public function edit(array $getParams)
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['login_error'] = 'Please sign in to edit a user.';
            header('Location: index.php?route=login');
            exit;
        }

        $id           = (int)($getParams['id'] ?? 0);
        $isOwnProfile = $id === (int) $_SESSION['user_id'];
        $viewerRole   = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');

        $formData = $this->showForm($id);
        if (!$formData) {
            $_SESSION['login_error'] = 'User not found.';
            header('Location: index.php?route=users');
            exit;
        }

        $targetUser = $formData['user'];
        $targetRole = strtoupper($targetUser['role'] ?? 'EMPLOYEE');

        if (!$this->canEditTarget($viewerRole, $targetRole, $isOwnProfile)) {
            require '../resources/views/errors/403.php';
            exit;
        }

        $user         = $targetUser;
        $departments  = $formData['departments'];
        $designations = $formData['designations'];
        $errors       = [];
        $old          = [];

        require '../resources/views/users/edit.php';
    }

    public function updateUser(array $getParams, array $postParams)
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['login_error'] = 'Please sign in to edit a user.';
            header('Location: index.php?route=login');
            exit;
        }

        $id           = (int)($getParams['id'] ?? 0);
        $isOwnProfile = $id === (int) $_SESSION['user_id'];
        $viewerRole   = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');

        $currentUser = $this->user->find($id)[0] ?? null;
        if (!$currentUser) {
            $_SESSION['login_error'] = 'User not found.';
            header('Location: index.php?route=users');
            exit;
        }

        $targetRole = strtoupper($currentUser['role'] ?? 'EMPLOYEE');
        if (!$this->canEditTarget($viewerRole, $targetRole, $isOwnProfile)) {
            require '../resources/views/errors/403.php';
            exit;
        }

        // Password changes are handled outside the profile edit form.
        unset($postParams['password'], $postParams['confirm_password']);

        if ($viewerRole === 'HR') {
            $postParams['role']           = $currentUser['role'];
            $postParams['designation_id'] = $currentUser['designation_id'];
        } elseif (!$this->isAdmin($viewerRole) && $isOwnProfile) {
            $postParams['department_id']  = $currentUser['department_id'];
            $postParams['designation_id'] = $currentUser['designation_id'];
            $postParams['role']           = $currentUser['role'];
        } elseif (!$this->isAdmin($viewerRole)) {
            $postParams['role'] = $currentUser['role'];
        }

        $result = $this->update($id, $postParams);

        if ($result['success']) {
            $file = $_FILES['profile_image'] ?? null;
            if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $imageResult = $this->handleProfileImageUpload($id, $file);
                if (!$imageResult['success']) {
                    $result['success'] = false;
                    $result['errors'] = array_merge($result['errors'], $imageResult['errors']);
                    $result['old'] = $postParams;
                    $this->logError('Profile image upload failed for user #' . $id . ': ' . json_encode($imageResult['errors']));
                }
            }
        }

        if ($result['success']) {
            if ($isOwnProfile) {
                $_SESSION['user_name']  = trim($postParams['name']);
                $_SESSION['user_email'] = strtolower(trim($postParams['email']));
            }
            $_SESSION['success'] = 'User details updated successfully!';
            header('Location: index.php?route=users');
            exit;
        }

        $formData = $this->showForm($id);
        if (!$formData) {
            $_SESSION['login_error'] = 'User not found.';
            header('Location: index.php?route=users');
            exit;
        }

        $user         = $formData['user'];
        $departments  = $formData['departments'];
        $designations = $formData['designations'];
        $errors       = $result['errors'];
        $old          = $result['old'];

        require '../resources/views/users/edit.php';
    }

    public function destroy(array $getParams)
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['login_error'] = 'Please sign in to delete a user.';
            header('Location: index.php?route=login');
            exit;
        }

        $id = (int)($getParams['id'] ?? 0);
        $targetUser = $this->user->find($id)[0] ?? null;
        if ($targetUser === null) {
            $_SESSION['login_error'] = 'User not found.';
            header('Location: index.php?route=users');
            exit;
        }

        $viewerRole = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');
        $targetRole = strtoupper($targetUser['role'] ?? 'EMPLOYEE');
        if (!$this->canManageTarget($viewerRole, $targetRole)) {
            require '../resources/views/errors/403.php';
            exit;
        }

        if ($this->delete($id)) {
            $_SESSION['success'] = 'User deleted successfully!';
        } else {
            $_SESSION['login_error'] = 'Failed to delete user.';
        }
        header('Location: index.php?route=users');
        exit;
    }

    private function canEditTarget(string $viewerRole, string $targetRole, bool $isOwnProfile): bool
    {
        $viewerRole = strtoupper($viewerRole);
        $targetRole = strtoupper($targetRole);

        if ($this->isAdmin($viewerRole)) {
            return true;
        }

        if ($viewerRole === 'MANAGER') {
            if ($isOwnProfile) {
                return true;
            }
            return $targetRole !== 'ADMIN';
        }

        if ($viewerRole === 'HR') {
            if ($isOwnProfile) {
                return true;
            }
            return !in_array($targetRole, ['ADMIN', 'MANAGER'], true);
        }

        return $isOwnProfile;
    }

    private function canManageTarget(string $viewerRole, string $targetRole): bool
    {
        $viewerRole = strtoupper($viewerRole);
        $targetRole = strtoupper($targetRole);

        if ($this->isAdmin($viewerRole)) {
            return true;
        }

        if ($viewerRole === 'MANAGER') {
            return $targetRole !== 'ADMIN';
        }

        if ($viewerRole === 'HR') {
            return $targetRole === 'EMPLOYEE';
        }

        return false;
    }

    private function isAdmin(string $role): bool
    {
        return strtoupper($role) === 'ADMIN';
    }

    private function getDepartments(): array
    {
        $stmt = $this->conn->query('SELECT * FROM departments');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getDesignations(): array
    {
        $stmt = $this->conn->query('SELECT * FROM designations');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function handleProfileImageUpload(int $userId, array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'errors' => ['profile_image' => 'Failed to upload profile image.'],
            ];
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 2 * 1024 * 1024;
        $mimeType = $file['type'] ?? '';
        if (!in_array($mimeType, $allowedTypes, true)) {
            return [
                'success' => false,
                'errors' => ['profile_image' => 'Invalid file type. Allowed: jpg, jpeg, png, webp.'],
            ];
        }

        if (($file['size'] ?? 0) > $maxSize) {
            return [
                'success' => false,
                'errors' => ['profile_image' => 'File exceeds maximum size of 2 MB.'],
            ];
        }

        $storageDir = __DIR__ . '/../../../storage/profile_images';
        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0777, true);
        }

        $ext = strtolower(pathinfo($file['name'] ?? 'profile.jpg', PATHINFO_EXTENSION));
        if ($ext === '') {
            $ext = 'jpg';
        }

        $filename = "profile_{$userId}.{$ext}";
        $destination = $storageDir . '/' . $filename;

        foreach (glob($storageDir . '/profile_' . $userId . '.*') as $existingFile) {
            @unlink($existingFile);
        }

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'success' => false,
                'errors' => ['profile_image' => 'Failed to store profile image.'],
            ];
        }

        $this->user->update($userId, ['profile_image' => $filename]);

        return [
            'success' => true,
            'errors' => [],
        ];
    }

    private function logError(string $message): void
    {
        $logFile = __DIR__ . '/../../../logs/errors.log';
        $line = '[' . date('c') . '] ' . $message . PHP_EOL;
        if (!is_dir(dirname($logFile))) {
            @mkdir(dirname($logFile), 0777, true);
        }
        @file_put_contents($logFile, $line, FILE_APPEND);
    }
}
