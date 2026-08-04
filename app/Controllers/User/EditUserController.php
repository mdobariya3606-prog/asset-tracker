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

    public function edit(array $getParams) {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['login_error'] = 'Please sign in to edit a user.';
            header('Location: index.php?route=login');
            exit;
        }
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
            require '../resources/views/errors/403.php';
            exit;
        }

        $id = (int)($getParams['id'] ?? 0);
        $formData = $this->showForm($id);
        if (!$formData) {
            $_SESSION['login_error'] = 'User not found.';
            header('Location: index.php?route=users');
            exit;
        }

        $user = $formData['user'];
        $departments = $formData['departments'];
        $designations = $formData['designations'];
        $errors = [];
        $old = [];

        require '../resources/views/users/edit.php';
    }

    public function updateUser(array $getParams, array $postParams) {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['login_error'] = 'Please sign in to edit a user.';
            header('Location: index.php?route=login');
            exit;
        }
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
            require '../resources/views/errors/403.php';
            exit;
        }

        $id = (int)($getParams['id'] ?? 0);
        $result = $this->update($id, $postParams);

        if ($result['success']) {
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

        $user = $formData['user'];
        $departments = $formData['departments'];
        $designations = $formData['designations'];
        $errors = $result['errors'];
        $old = $result['old'];

        require '../resources/views/users/edit.php';
    }

    public function destroy(array $getParams) {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['login_error'] = 'Please sign in to delete a user.';
            header('Location: index.php?route=login');
            exit;
        }
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
            require '../resources/views/errors/403.php';
            exit;
        }

        $id = (int)($getParams['id'] ?? 0);
        if ($this->delete($id)) {
            $_SESSION['success'] = 'User deleted successfully!';
        } else {
            $_SESSION['login_error'] = 'Failed to delete user.';
        }
        header('Location: index.php?route=users');
        exit;
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
}
