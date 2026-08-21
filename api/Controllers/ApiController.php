<?php

declare(strict_types=1);

namespace Api\Controllers;

use Api\Response;
use PDO;
use Throwable;

/**
 * Central API dispatcher.
 *
 * This class does not contain business logic. It only identifies the
 * requested resource and forwards the request to the correct controller.
 */
final class ApiController
{
    private array $controllers;

    public function __construct(PDO $conn)
    {
        // Each resource has its own controller and business logic.
        $this->controllers = [
            'auth' => new AuthApiController($conn),
            'departments' => new DepartmentApiController($conn),
            'designations' => new DesignationApiController($conn),
            'assets' => new AssetApiController($conn),
            'users' => new UserApiController($conn),
            'notices' => new NoticeApiController($conn),
        ];
    }

    public function dispatch(string $api, string $method, array $query, array $body): never
    {
        // API names are supplied as: api=assets or api=assets/show.
        $api = trim($api, '/');
        if ($api === '') {
            Response::error('API_REQUIRED', 'The api query parameter is required.', 400);
        }

        [$resource, $action] = array_pad(explode('/', $api, 2), 2, null);
        
        if (!isset($this->controllers[$resource])) {
            Response::error('NOT_FOUND', 'API endpoint not found.', 404);
        }

        try {
            // Resource controllers finish requests through Api\Response.
            $this->controllers[$resource]->handle($action, strtoupper($method), $query, $body);
        } catch (Throwable $exception) {
            // Keep internal database details out of the JSON response.
            error_log($exception->getMessage());
            Response::error('SERVER_ERROR', 'The request could not be completed.', 500);
        }

        Response::error('NOT_FOUND', 'API endpoint not found.', 404);
    }
}
