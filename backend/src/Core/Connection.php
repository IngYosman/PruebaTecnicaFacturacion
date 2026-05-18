<?php

namespace App\Core;

class Connection
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getConnection()
    {
        $dsn = "mysql:
            host={$this->config['host']};
            port={$this->config['port']};
            dbname={$this->config['database']};
            charset={$this->config['charset']}";

        try {
            $pdo = new \PDO($dsn, $this->config['username'], $this->config['password']);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

            return $pdo;
        } catch (\PDOException $e) {
            throw new \Exception("Error al conectar a la base de datos: " . $e->getMessage());
        }
    }
}
