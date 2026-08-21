<?php

declare(strict_types=1);

namespace Api\Controllers;

use Api\Response;
use App\Models\Asset;

/** Handles asset CRUD, filters, details, and assignment history. */
final class AssetApiController extends BaseApiController
{
    public function handle(?string $action, string $method, array $query, array $body): never
    {
        $this->requireAuth();
        $model = new Asset($this->conn);
        // Action routes are delegated to their own controller instead of
        // mixing asset-request logic into this asset controller.
        if ($action === 'show' || ($action === null && isset($query['id']))) {
            $this->one($model->find($this->id($query)), 'ASSET_NOT_FOUND', 'Asset');
        }
        if ($action === 'history') {
            Response::send($model->getAssignmentHistory($this->id($query)));
        }
        if ($action === 'requests') {
            (new RequestApiController($this->conn))->handle(null, $method, $query, $body);
        }
        if ($action === 'request') {
            (new RequestApiController($this->conn))->create($method, $query, $body);
        }
        if ($method === 'GET') {
            // Asset::all applies the same category, status, and search filters
            // used by the existing assets page.
            $data = $model->all(isset($query['category_id']) ? (int)$query['category_id'] : null, $query['status'] ?? null, $query['search'] ?? null);
            Response::send($data, 200, ['count' => count($data)]);
        }
        if ($method === 'POST') {
            // Validate first; only valid data reaches the model's insert method.
            $errors = $model->validate($body);
            if ($errors !== []) Response::error('VALIDATION_FAILED', 'Asset validation failed.', 422, $errors);
            Response::send(['id' => $model->create($body)], 201);
        }
        if ($method === 'PUT' || $method === 'PATCH') {
            // PUT and PATCH both use the existing partial update model method.
            $id = $this->id($query);
            if (!$model->find($id)) Response::error('ASSET_NOT_FOUND', 'Asset not found.', 404);
            $model->update($id, $body);
            Response::send($model->find($id));
        }
        if ($method === 'DELETE') {
            // Confirm the record exists before deleting it.
            $id = $this->id($query);
            if (!$model->find($id)) Response::error('ASSET_NOT_FOUND', 'Asset not found.', 404);
            $model->delete($id);
            Response::send(['id' => $id, 'deleted' => true]);
        }
        Response::error('METHOD_NOT_ALLOWED', 'Use GET, POST, PUT, PATCH, or DELETE for assets.', 405);
    }
}
