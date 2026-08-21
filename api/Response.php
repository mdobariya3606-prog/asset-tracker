<?php

declare(strict_types=1);

namespace Api;

final class Response
{
    public static function send(mixed $data = null, int $status = 200, array $meta = []): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        $body = ['success' => $status < 400];
        if ($status < 400) {
            $body['data'] = $data;
            if ($meta !== []) {
                $body['meta'] = $meta;
            }
        } else {
            $body['error'] = $data;
        }

        echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        exit;
    }

    public static function error(string $code, string $message, int $status, array $details = []): never
    {
        $error = ['code' => $code, 'message' => $message];
        if ($details !== []) {
            $error['details'] = $details;
        }
        self::send($error, $status);
    }
}
