<?php

namespace App\Controllers;

use App\Core\DatabaseManager;
use App\Models\Invoice;
use App\Utils\ResponseApi;
use App\Utils\AuthMiddleware;

class InvoiceController
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

            $sql = "SELECT i.*, c.business_name, c.document_number 
                    FROM invoice i 
                    INNER JOIN client c ON i.client_id = c.id 
                    WHERE 1=1";
            $params = [];

            if (!empty($_GET['number'])) {
                $sql .= " AND i.number LIKE ?";
                $params[] = '%' . $_GET['number'] . '%';
            }

            if (!empty($_GET['client_id'])) {
                $sql .= " AND i.client_id = ?";
                $params[] = $_GET['client_id'];
            }

            $sql .= " ORDER BY i.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $invoices = $stmt->fetchAll();

            ResponseApi::success('Facturas obtenidas correctamente.', $invoices, 200);

        } catch (\Exception $e) {
            ResponseApi::error('Error interno del servidor.', 500);
        } finally {
            $this->db = null;
        }
    }

    public function show($id): void
    {
        try {
            AuthMiddleware::verify();

            $stmt = $this->db->prepare("SELECT i.*, c.business_name, c.document_number 
                                        FROM invoice i 
                                        INNER JOIN client c ON i.client_id = c.id 
                                        WHERE i.id = ?");
            $stmt->execute([$id]);
            $invoice = $stmt->fetch();

            if (!$invoice) {
                ResponseApi::error('Factura no encontrada.', 404);
            }

            ResponseApi::success('Factura obtenida correctamente.', $invoice, 200);

        } catch (\Exception $e) {
            ResponseApi::error('Error interno del servidor.', 500);
        } finally {
            $this->db = null;
        }
    }

    public function create(): void
    {
        try {
            AuthMiddleware::verify();

            $json = file_get_contents('php://input');
            $input = json_decode($json, true);

            $errors = [];

            if (empty($input['number'])) {
                $errors['number'] = ['El número de factura es obligatorio.'];
            }

            if (empty($input['client_id'])) {
                $errors['client_id'] = ['El cliente es obligatorio.'];
            }

            if (empty($input['issue_date'])) {
                $errors['issue_date'] = ['La fecha de emisión es obligatoria.'];
            }

            if (empty($input['due_date'])) {
                $errors['due_date'] = ['La fecha de vencimiento es obligatoria.'];
            }

            if (isset($input['subtotal']) && $input['subtotal'] < 0) {
                $errors['subtotal'] = ['El subtotal no puede ser negativo.'];
            }

            if (isset($input['tax_amount']) && ($input['tax_amount'] < 0 || $input['tax_amount'] > 100)) {
                $errors['tax_amount'] = ['El IVA debe estar entre 0 y 100.'];
            }

            if (isset($input['total']) && $input['total'] < 0) {
                $errors['total'] = ['El total no puede ser negativo.'];
            }

            if (!empty($input['due_date']) && !empty($input['issue_date'])) {
                if ($input['due_date'] < $input['issue_date']) {
                    $errors['due_date'] = ['La fecha de vencimiento no puede ser menor que la fecha de emisión.'];
                }
            }

            if (isset($input['subtotal']) && isset($input['tax_amount']) && isset($input['total'])) {
                $taxValue = $input['subtotal'] * ($input['tax_amount'] / 100);
                $expectedTotal = $input['subtotal'] + $taxValue;
                if (abs($expectedTotal - $input['total']) > 0.01) {
                    $errors['total'] = ['El total debe ser igual a subtotal más IVA.'];
                }
            }

            if (!empty($errors)) {
                ResponseApi::error('Error de validación.', 422, $errors);
            }

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT id FROM invoice WHERE number = ?");
            $stmt->execute([$input['number']]);
            if ($stmt->fetch()) {
                $this->db->rollBack();
                ResponseApi::error('El número de factura ya existe.', 409, ['number' => ['Debe ser único.']]);
            }

            $stmt = $this->db->prepare("SELECT id FROM client WHERE id = ?");
            $stmt->execute([$input['client_id']]);
            if (!$stmt->fetch()) {
                $this->db->rollBack();
                ResponseApi::error('El cliente no existe.', 422);
            }

            $invoice = new Invoice();
            $invoice->setNumber($input['number']);
            $invoice->setClientId($input['client_id']);
            $invoice->setIssueDate($input['issue_date']);
            $invoice->setDueDate($input['due_date']);
            $invoice->setPaymentMethod($input['payment_method'] ?? null);
            $invoice->setSubtotal($input['subtotal'] ?? 0);
            $invoice->setTaxAmount($input['tax_amount'] ?? 0);
            $invoice->setTotal($input['total'] ?? 0);
            $invoice->setNotes($input['notes'] ?? null);

            $stmt = $this->db->prepare("INSERT INTO invoice (number, client_id, issue_date, due_date, payment_method, subtotal, tax_amount, total, notes) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $invoice->getNumber(),
                $invoice->getClientId(),
                $invoice->getIssueDate(),
                $invoice->getDueDate(),
                $invoice->getPaymentMethod(),
                $invoice->getSubtotal(),
                $invoice->getTaxAmount(),
                $invoice->getTotal(),
                $invoice->getNotes()
            ]);

            $this->db->commit();

            $invoiceId = $this->db->lastInsertId();

            ResponseApi::success('Factura creada correctamente.', ['id' => $invoiceId, 'number' => $invoice->getNumber(), 'total' => $invoice->getTotal()], 201);

        } catch (\PDOException $e) {
            if (isset($this->db) && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            if ($e->getCode() === '23000') {
                ResponseApi::error('El número de factura ya existe.', 409, ['number' => ['Debe ser único.']]);
            }
            ResponseApi::error('Error interno del servidor.', 500);
        } catch (\Exception $e) {
            if (isset($this->db) && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            ResponseApi::error('Error interno del servidor.', 500);
        } finally {
            $this->db = null;
        }
    }

    public function update($id): void
    {
        try {
            AuthMiddleware::verify();

            $stmt = $this->db->prepare("SELECT * FROM invoice WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();

            if (!$existing) {
                ResponseApi::error('Factura no encontrada.', 404);
            }

            $json = file_get_contents('php://input');
            $input = json_decode($json, true);

            $number = $input['number'] ?? $existing['number'];
            $clientId = $input['client_id'] ?? $existing['client_id'];
            $issueDate = $input['issue_date'] ?? $existing['issue_date'];
            $dueDate = $input['due_date'] ?? $existing['due_date'];
            $subtotal = isset($input['subtotal']) ? $input['subtotal'] : $existing['subtotal'];
            $taxAmount = isset($input['tax_amount']) ? $input['tax_amount'] : $existing['tax_amount'];
            $total = isset($input['total']) ? $input['total'] : $existing['total'];
            $paymentMethod = isset($input['payment_method']) ? $input['payment_method'] : $existing['payment_method'];
            $notes = isset($input['notes']) ? $input['notes'] : $existing['notes'];

            $errors = [];

            $stmt = $this->db->prepare("SELECT id FROM invoice WHERE number = ? AND id != ?");
            $stmt->execute([$number, $id]);
            if ($stmt->fetch()) {
                $errors['number'] = ['El número de factura ya existe.'];
            }

            $stmt = $this->db->prepare("SELECT id FROM client WHERE id = ?");
            $stmt->execute([$clientId]);
            if (!$stmt->fetch()) {
                $errors['client_id'] = ['El cliente no existe.'];
            }

            if ($dueDate < $issueDate) {
                $errors['due_date'] = ['La fecha de vencimiento no puede ser menor que la fecha de emisión.'];
            }

            if ($subtotal < 0) {
                $errors['subtotal'] = ['El subtotal no puede ser negativo.'];
            }

            if ($taxAmount < 0 || $taxAmount > 100) {
                $errors['tax_amount'] = ['El IVA debe estar entre 0 y 100.'];
            }

            if ($total < 0) {
                $errors['total'] = ['El total no puede ser negativo.'];
            }

            $taxValue = $subtotal * ($taxAmount / 100);
            $expectedTotal = $subtotal + $taxValue;
            if (abs($expectedTotal - $total) > 0.01) {
                $errors['total'] = ['El total debe ser igual a subtotal más IVA.'];
            }

            if (!empty($errors)) {
                ResponseApi::error('Error de validación.', 422, $errors);
            }

            $this->db->beginTransaction();

            $invoice = new Invoice();
            $invoice->setId($id);
            $invoice->setNumber($number);
            $invoice->setClientId($clientId);
            $invoice->setIssueDate($issueDate);
            $invoice->setDueDate($dueDate);
            $invoice->setPaymentMethod($paymentMethod);
            $invoice->setSubtotal($subtotal);
            $invoice->setTaxAmount($taxAmount);
            $invoice->setTotal($total);
            $invoice->setNotes($notes);

            $fields = [];
            $params = [];

            $allowedFields = ['number', 'client_id', 'issue_date', 'due_date', 'payment_method', 'subtotal', 'tax_amount', 'total', 'notes'];

            foreach ($allowedFields as $field) {
                if (isset($input[$field])) {
                    $fields[] = "$field = ?";
                    $params[] = $input[$field];
                }
            }

            if (empty($fields)) {
                ResponseApi::error('No se proporcionaron datos para actualizar.', 400);
            }

            $params[] = $id;
            $sql = "UPDATE invoice SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            $this->db->commit();

            ResponseApi::success('Factura actualizada correctamente.', null, 200);

        } catch (\PDOException $e) {
            if (isset($this->db) && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            if ($e->getCode() === '23000') {
                ResponseApi::error('El número de factura ya existe.', 409, ['number' => ['Debe ser único.']]);
            }
            ResponseApi::error('Error interno del servidor.', 500);
        } catch (\Exception $e) {
            if (isset($this->db) && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            ResponseApi::error('Error interno del servidor.', 500);
        } finally {
            $this->db = null;
        }
    }

    public function delete($id): void
    {
        try {
            AuthMiddleware::verify();

            $stmt = $this->db->prepare("SELECT id FROM invoice WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                ResponseApi::error('Factura no encontrada.', 404);
            }

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("DELETE FROM invoice WHERE id = ?");
            $stmt->execute([$id]);

            $this->db->commit();

            ResponseApi::success('Factura eliminada correctamente.', null, 200);

        } catch (\Exception $e) {
            if (isset($this->db) && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            ResponseApi::error('Error interno del servidor.', 500);
        } finally {
            $this->db = null;
        }
    }
}
