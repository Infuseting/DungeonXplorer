<?php

namespace App\Config;

use mysqli;

/**
 * Classe Database
 * 
 * Gestionnaire de connexion à la base de données utilisant le pattern Singleton.
 * Assure qu'une seule instance de connexion MySQLi est active.
 */
class Database
{
    /** @var Database|null Instance unique de la classe */
    private static $instance = null;

    /** @var mysqli Instance de connexion MySQLi */
    private $connection;

    /**
     * Constructeur privé.
     * Initialise la connexion à la base de données en récupérant les informations
     * depuis les variables d'environnement (`.env` ou `getenv`).
     * 
     * @throws \Exception En cas d'échec de connexion.
     */
    private function __construct()
    {
        // Récupération de la configuration avec valeurs par défaut
        $host = $_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?? '127.0.0.1';
        $db = $_ENV['DB_DATABASE'] ?? $_SERVER['DB_DATABASE'] ?? getenv('DB_DATABASE') ?? 'dungeonxplorer';
        $user = $_ENV['DB_USERNAME'] ?? $_SERVER['DB_USERNAME'] ?? getenv('DB_USERNAME') ?? 'root';
        $pass = $_ENV['DB_PASSWORD'] ?? $_SERVER['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '';
        $port = $_ENV['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? getenv('DB_PORT') ?? '3306';
        $charset = 'utf8mb4';

        // Activation des rapports d'erreurs stricts pour MySQLi
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $this->connection = new mysqli($host, $user, $pass, $db, (int) $port);
            $this->connection->set_charset($charset);
        } catch (\Exception $e) {
            throw new \Exception("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    /**
     * Récupère l'instance unique de la connexion.
     * Crée l'instance si elle n'existe pas encore.
     * 
     * @return Database L'instance du gestionnaire de base de données.
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retourne l'objet de connexion MySQLi natif.
     * 
     * @return mysqli La connexion active.
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
