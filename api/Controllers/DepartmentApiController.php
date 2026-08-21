<?php

declare(strict_types=1);

namespace Api\Controllers;

use Api\Response;
use App\Models\Department;

/** Handles department listing, lookup, validation, and creation. */
final class DepartmentApiController extends BaseApiController
{
                                //null,           $_GET,            [],         []
    public function handle(?string $action, string $method, array $query, array $body): never
    {
        // Departments are protected in the same way as the existing web page.
        $this->requireAuth();
        $model = new Department($this->conn);

        if ($action === 'show' || isset($query['id'])) {
            $department = $model->find($this->id($query));
            $this->one($department, 'DEPARTMENT_NOT_FOUND', 'Department');
        }

        if ($method === 'GET') {
            Response::send($model->all());
        }
        
        if ($method === 'POST') {
            // Reuse the application's model validation before inserting.
            $errors = $model->validate($body);
            if ($errors !== []) {
                Response::error('VALIDATION_FAILED', 'Department validation failed.', 422, $errors);
            }
            $model->create($body);
            Response::send(['created' => true], 201);
        }
        Response::error('METHOD_NOT_ALLOWED', 'Use GET or POST for departments.', 405);
    }
}
