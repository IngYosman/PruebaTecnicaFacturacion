<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\DatabaseManager;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
