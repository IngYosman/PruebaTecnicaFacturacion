<?php

namespace App\Utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    public static function verify()
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        $parts = explode(' ', $authHeader);

        if (count($parts) !== 2 || $parts[0] !== 'Bearer') {
            ResponseApi::error('Token no proporcionado.', 401);
        }

        $token = $parts[1];
        $secret = $_ENV['JWT_SECRET'];

        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            return (array) $decoded->data ?? [];
        } catch (\Exception $e) {
            ResponseApi::error('Token inválido o expirado.', 401);
        }
    }
}
