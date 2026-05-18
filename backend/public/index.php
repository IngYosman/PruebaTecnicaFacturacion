<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

use Bramus\Router\Router;
use App\Controllers\AuthController;
use App\Controllers\ClientController;
use App\Controllers\InvoiceController;
use App\Controllers\DashboardController;

$router = new Router();

$auth = new AuthController();
$clients = new ClientController();
$invoices = new InvoiceController();
$dashboard = new DashboardController();

$router->post('/api/auth/login', [$auth, 'login']);
$router->get('/api/auth/me', [$auth, 'me']);

$router->get('/api/clients', [$clients, 'index']);

$router->get('/api/dashboard', [$dashboard, 'index']);

$router->get('/api/invoices', [$invoices, 'index']);
$router->get('/api/invoices/(\d+)', [$invoices, 'show']);
$router->post('/api/invoices', [$invoices, 'create']);
$router->put('/api/invoices/(\d+)', [$invoices, 'update']);
$router->delete('/api/invoices/(\d+)', [$invoices, 'delete']);

$router->run();
