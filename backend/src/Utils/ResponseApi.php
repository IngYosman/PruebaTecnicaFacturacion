<?php

namespace App\Utils;

class ResponseApi
{
    public static function json($data, int $code = 200): void
    {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    public static function success(string $message, $data = null, int $code = 200): void
    {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public static function error(string $message, int $code = 400, array $errors = []): void
    {
        $response = [
            'success' => false,
            'message' => $message,
            'errors'=> $errors
        ];

        self::json($response, $code);
    }
}
