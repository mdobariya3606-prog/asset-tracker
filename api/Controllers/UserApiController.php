<?php

declare(strict_types=1);

namespace Api\Controllers;

use Api\Response;
use App\Models\Dashboard;
use App\Models\Designation;
use App\Models\User;

/** Handles user listing, filtering, profile, dashboard data, and CRUD. */
final class UserApiController extends BaseApiController
{
    public function handle(?string $action, string $method, array $query, array $body): never
    {
        $this->requireAuth();
        $model = new User($this->conn);
        if ($action === 'dashboard-data') {
            // Dashboard data depends on the current session user and role.
            $user = $model->find((int)$_SESSION['user_id'])[0] ?? null;
            if (!$user) Response::error('USER_NOT_FOUND', 'Current user not found.', 404);
            Response::send((new Dashboard($this->conn))->data((int)$user['id'], (string)$user['role']));
        }
        if ($action === 'designations') {
            Response::send((new Designation($this->conn))->getByDepartmentId($this->id($query, 'department_id')));
        }
        if ($action === 'profile') $this->one($model->find((int)$_SESSION['user_id']), 'USER_NOT_FOUND', 'User');
        if ($action === 'show' || ($action === null && isset($query['id']))) $this->one($model->find($this->id($query)), 'USER_NOT_FOUND', 'User');
        if ($method === 'GET') {
            // Pagination and filters are passed to the existing User model.
            $page = max(1, (int)($query['page'] ?? 1));
            $perPage = min(100, max(1, (int)($query['per_page'] ?? 20)));
            $search = (string)($query['search'] ?? '');
            $department = isset($query['department_id']) ? (int)$query['department_id'] : null;
            $designation = isset($query['designation_id']) ? (int)$query['designation_id'] : null;
            $role = $query['role'] ?? null;
            $data = $model->paginate($page, $perPage, $search, (string)($query['sort'] ?? 'id'), (string)($query['order'] ?? 'asc'), $department, $designation, $role);
            Response::send($data, 200, ['page' => $page, 'per_page' => $perPage, 'total' => $model->count($search, $department, $designation, $role)]);
        }
        if ($method === 'POST') {
            Response::send(['id' => (int)$model->create($body)], 201);
        }
        if ($method === 'PUT' || $method === 'PATCH') {
            $id = $this->id($query);
            if ($model->find($id) === []) Response::error('USER_NOT_FOUND', 'User not found.', 404);
            $model->update($id, $body);
            Response::send($model->find($id)[0] ?? []);
        }
        if ($method === 'DELETE') {
            $id = $this->id($query);
            if (!$model->softDelete($id)) Response::error('USER_NOT_FOUND', 'User not found.', 404);
            Response::send(['id' => $id, 'deleted' => true]);
        }
        Response::error('METHOD_NOT_ALLOWED', 'Unsupported method for users.', 405);
    }
}
