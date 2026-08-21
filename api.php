<?php

declare(strict_types=1);

require_once __DIR__ . '/api/bootstrap.php';
require_once __DIR__ . '/api/Response.php';
require_once __DIR__ . '/api/Controllers/BaseApiController.php';
require_once __DIR__ . '/api/Controllers/AuthApiController.php';
require_once __DIR__ . '/api/Controllers/DepartmentApiController.php';
require_once __DIR__ . '/api/Controllers/DesignationApiController.php';
require_once __DIR__ . '/api/Controllers/RequestApiController.php';
require_once __DIR__ . '/api/Controllers/AssetApiController.php';
require_once __DIR__ . '/api/Controllers/UserApiController.php';
require_once __DIR__ . '/api/Controllers/NoticeApiController.php';
require_once __DIR__ . '/api/Controllers/ApiController.php';

use Api\Controllers\ApiController;
use Api\Response;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$rawBody = file_get_contents('php://input');
$body = json_decode($rawBody ?: '[]', true);

if (!is_array($body)) {
    Response::error('INVALID_JSON', 'Request body must be a valid JSON object.', 400);
}

(new ApiController($conn))->dispatch(
    (string)($_GET['api'] ?? ''),
    (string)($_SERVER['REQUEST_METHOD'] ?? 'GET'),
    $_GET,
    $body
);
