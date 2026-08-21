<?php

declare(strict_types=1);

namespace Api\Controllers;

use Api\Response;
use PDO;

/**
 * Shared helpers for API controllers.
 *
 * Resource controllers inherit these helpers for authentication, ID
 * validation, and consistent not-found responses.
 */
abstract class BaseApiController
{
    public function __construct(protected readonly PDO $conn)
    {
    }

    protected function requireAuth(): void
    {
        // The API uses the same PHP session as the existing web application.
        if (empty($_SESSION['user_id'])) {
            Response::error('AUTHENTICATION_REQUIRED', 'Authentication is required.', 401);
        }
    }

    protected function id(array $query, string $key = 'id'): int
    {
        // Resource IDs are read from the query string, for example id=12.
        $id = filter_var($query[$key] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            Response::error('INVALID_ID', 'A valid positive id is required.', 400);
        }
        return $id;
    }

    protected function one(array $data, string $code, string $name): never
    {
        // Models return an empty array when a record does not exist.
        if ($data === []) {
            Response::error($code, $name . ' not found.', 404);
        }
        Response::send($data);
    }
}
