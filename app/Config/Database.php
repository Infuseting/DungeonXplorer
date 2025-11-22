<?php

namespace App\Config;

use mysqli;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $host = $_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?? '127.0.0.1';
        $db   = $_ENV['DB_DATABASE'] ?? $_SERVER['DB_DATABASE'] ?? getenv('DB_DATABASE') ?? 'dungeonxplorer';
        $user = $_ENV['DB_USERNAME'] ?? $_SERVER['DB_USERNAME'] ?? getenv('DB_USERNAME') ?? 'root';
        $pass = $_ENV['DB_PASSWORD'] ?? $_SERVER['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '';
        $port = $_ENV['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? getenv('DB_PORT') ?? '3306';
        $charset = 'utf8mb4';
        // Enable error reporting for mysqli
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $this->connection = new mysqli($host, $user, $pass, $db, (int)$port);
            $this->connection->set_charset($charset);
        } catch (\Exception $e) {
            throw new \Exception("Database Connection Error: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
