<?php

namespace App\Controllers;

use App\Core\DatabaseManager;
use App\Utils\ResponseApi;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{
    private $db;
    private string $secret;

    public function __construct()
    {
        $dbManager = new DatabaseManager();
        $this->db = $dbManager->getManager('default')->getConnection();
        $this->secret = $_ENV['JWT_SECRET'];
    }

    public function login(): void
    {
        try {
            $json = file_get_contents('php://input');
            $input = json_decode($json, true);

            if (empty($input['email']) || empty($input['password'])) {
                ResponseApi::error('El correo y la contraseña son obligatorios.', 422);
            }

            $stmt = $this->db->prepare("SELECT id, email, password, name, last_name, role, status FROM user WHERE email = ? AND deleted_at IS NULL");
            $stmt->execute([$input['email']]);
            $user = $stmt->fetch();

            if (!$user) {
                ResponseApi::error('Credenciales incorrectas.', 401);
            }

            if (!password_verify($input['password'], $user['password'])) {
                ResponseApi::error('Credenciales incorrectas.', 401);
            }

            if ($user['status'] !== 1) {
                ResponseApi::error('El usuario se encuentra desactivado.', 403);
            }

            $data = [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['name'],
                'last_name' => $user['last_name'],
                'role' => $user['role']
            ];

            $payload = [
                'iss' => 'facturacion-api',
                'iat' => time(),
                'exp' => time() + (60 * 60 * 24),
                'data' => $data
            ];

            $token = JWT::encode($payload, $this->secret, 'HS256');

            ResponseApi::success('Inicio de sesión exitoso.', [
                'token' => $token,
                'user' => $data
            ], 200);

        } catch (\Exception $e) {
            ResponseApi::error('Error interno del servidor.', 500);
        } finally {
            $this->db = null;
        }
    }

    public function me(): void
    {
        try {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

            $parts = explode(' ', $authHeader);
            if (count($parts) !== 2 || $parts[0] !== 'Bearer') {
                ResponseApi::error('Token no proporcionado.', 401);
            }

            $token = $parts[1];

            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            $userId = $decoded->data->id;

            $stmt = $this->db->prepare("SELECT id, email, name, last_name, phone, role, status, created_at FROM user WHERE id = ? AND deleted_at IS NULL");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!$user) {
                ResponseApi::error('Usuario no encontrado.', 404);
            }

            ResponseApi::success('Usuario autenticado.', $user, 200);

        } catch (\Exception $e) {
            ResponseApi::error('Token inválido o expirado.', 401);
        } finally {
            $this->db = null;
        }
    }
}
