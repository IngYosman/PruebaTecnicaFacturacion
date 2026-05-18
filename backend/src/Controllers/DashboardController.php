<?php

namespace App\Controllers;

use App\Core\DatabaseManager;
use App\Utils\ResponseApi;
use App\Utils\AuthMiddleware;

class DashboardController
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

            $stmt = $this->db->query("SELECT COUNT(*) as total FROM invoice");
            $totalInvoices = $stmt->fetch()['total'];

            $stmt = $this->db->query("SELECT COALESCE(SUM(total), 0) as total_facturado FROM invoice");
            $totalFacturado = $stmt->fetch()['total_facturado'];

            $stmt = $this->db->query("SELECT COUNT(*) as total FROM client WHERE deleted_by IS NULL");
            $totalClients = $stmt->fetch()['total'];

            $stmt = $this->db->query("SELECT i.*, c.business_name 
                                      FROM invoice i 
                                      INNER JOIN client c ON i.client_id = c.id 
                                      ORDER BY i.created_at DESC 
                                      LIMIT 5");
            $latestInvoices = $stmt->fetchAll();

            $data = [
                'total_invoices' => $totalInvoices,
                'total_facturado' => $totalFacturado,
                'total_clients' => $totalClients,
                'latest_invoices' => $latestInvoices
            ];

            ResponseApi::success('Dashboard obtenido correctamente.', $data, 200);

        } catch (\Exception $e) {
            ResponseApi::error('Error interno del servidor.', 500);
        } finally {
            $this->db = null;
        }
    }
}
