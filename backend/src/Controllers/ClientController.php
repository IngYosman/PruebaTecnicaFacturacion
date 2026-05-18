<?php

namespace App\Controllers;

use App\Core\DatabaseManager;
use App\Utils\ResponseApi;
use App\Utils\AuthMiddleware;

class ClientController
{
    private $db;

    public function __construct()
    {
        $dbManager = new DatabaseManager();
        $this->db = $dbManager->getManager('default')->getConnection();
    }

    public function index(): void
    {
        try {
            AuthMiddleware::verify();

            $stmt = $this->db->prepare("SELECT id, document_number, business_name, email, address FROM client WHERE deleted_by IS NULL ORDER BY business_name ASC");
            $stmt->execute();
            $clients = $stmt->fetchAll();

            ResponseApi::success('Clientes obtenidos correctamente.', $clients, 200);

        } catch (\Exception $e) {
            ResponseApi::error('Error interno del servidor.', 500);
        } finally {
            $this->db = null;
        }
    }
}
