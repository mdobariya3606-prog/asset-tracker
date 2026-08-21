<?php

declare(strict_types=1);

namespace Api\Controllers;

use Api\Response;
use App\Models\Designation;

/** Handles designation listing, lookup, validation, and creation. */
final class DesignationApiController extends BaseApiController
{
    public function handle(?string $action, string $method, array $query, array $body): never
    {
        // A designation belongs to a department; model validation checks that relation.
        $this->requireAuth();
        $model = new Designation($this->conn);
        if ($action === 'show' || isset($query['id'])) {
            $this->one($model->find($this->id($query)), 'DESIGNATION_NOT_FOUND', 'Designation');
        }
        if ($method === 'GET') {
            Response::send($model->all());
        }
        if ($method === 'POST') {
            $errors = $model->validate($body);
            if ($errors !== []) {
                Response::error('VALIDATION_FAILED', 'Designation validation failed.', 422, $errors);
            }
            $model->create($body);
            Response::send(['created' => true], 201);
        }
        Response::error('METHOD_NOT_ALLOWED', 'Use GET or POST for designations.', 405);
    }
}
