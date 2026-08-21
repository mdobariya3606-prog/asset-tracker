<?php

declare(strict_types=1);

namespace Api\Controllers;

use Api\Response;
use App\Models\AssetRequest;
use PDO;

/** Handles asset-request listing, lookup, and request creation. */
final class RequestApiController extends BaseApiController
{
    public function handle(?string $action, string $method, array $query, array $body): never
    {
        $this->requireAuth();
        if ($action === 'show' || isset($query['id'])) {
            // This direct lookup avoids the web model's HTML 404 response.
            $stmt = $this->conn->prepare('SELECT * FROM asset_requests WHERE id = ?');
            $stmt->execute([$this->id($query)]);
            $this->one($stmt->fetch(PDO::FETCH_ASSOC) ?: [], 'REQUEST_NOT_FOUND', 'Asset request');
        }
        if ($method !== 'GET') Response::error('METHOD_NOT_ALLOWED', 'Only GET is supported for asset request lists.', 405);
        // AssetRequest::filtered applies the current user's visibility rules.
        $data = (new AssetRequest($this->conn))->filtered($query['status'] ?? null, $query['date_from'] ?? null, $query['date_to'] ?? null);
        Response::send($data, 200, ['count' => count($data)]);
    }

    public function create(string $method, array $query, array $body): never
    {
        $this->requireAuth();
        if ($method !== 'POST') Response::error('METHOD_NOT_ALLOWED', 'Use POST for assets/request.', 405);
        $assetId = $this->id($query);
        // Reuse the same reason and due-date validation as the web form.
        $validation = (new AssetRequest($this->conn))->validate($body);
        if ($validation !== []) Response::error('VALIDATION_FAILED', 'Asset request validation failed.', 422, $validation);
        // Check availability before creating the request.
        $stmt = $this->conn->prepare('SELECT id, name, status FROM assets WHERE id = ?');
        $stmt->execute([$assetId]);
        $asset = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$asset) Response::error('ASSET_NOT_FOUND', 'Asset not found.', 404);
        if ($asset['status'] !== 'AVAILABLE') Response::error('ASSET_UNAVAILABLE', 'The asset is not available.', 409);
        $insert = $this->conn->prepare('INSERT INTO asset_requests (user_id, asset_id, asset_name, reason, due_date) VALUES (?, ?, ?, ?, ?)');
        $insert->execute([(int)$_SESSION['user_id'], $assetId, $asset['name'], trim((string)$body['reason']), $body['due_date']]);
        Response::send(['id' => (int)$this->conn->lastInsertId()], 201);
    }
}
