<?php

namespace App\Core;

use Symfony\Component\Yaml\Yaml;

class DatabaseManager
{
    private array $databases;

    public function __construct()
    {
        $configPath = __DIR__ . '/../../config/config.yaml';
        $yamlContent = file_get_contents($configPath);

        $yamlContent = preg_replace_callback('/%([A-Z_]+)%/', function ($matches) {
            return $_ENV[$matches[1]] ?? '';
        }, $yamlContent);

        $config = Yaml::parse($yamlContent);
        $this->databases = $config['databases'];
    }

    public function getManager($name = 'default')
    {
        if (!isset($this->databases[$name])) {
            throw new \Exception("El manager '{$name}' no existe en la configuracion.");
        }

        return new Connection($this->databases[$name]);
    }
}
