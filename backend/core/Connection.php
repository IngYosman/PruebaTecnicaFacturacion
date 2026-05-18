<?php

namespace App;

class Connection
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getConnection()
    {
        $dsn = "mysql:host={$this->config['host']};port={$this->config['port']};dbname={$this->config['database']};charset={$this->config['charset']}";

        try {
            return new \PDO($dsn, $this->config['username'], $this->config['password']);
        } catch (\PDOException $e) {
            throw new \Exception("Error al conectar a la base de datos: " . $e->getMessage());
        }
    }
}
