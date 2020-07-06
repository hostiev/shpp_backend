<?php


namespace database;


use PDO;
use PDOException;
use app\core\Config;

/**
 * Manages PDO connection.
 */
class Connection
{
    private static $instance = null;
    private $connection;

    /**
     * Constructs a PDO instance.
     */
    private function __construct() {
        try {
            $config = Config::getInstance();
            $this->connection = new PDO(
                $config->getConfig('DSN'),
                $config->getConfig('dbUser'),
                $config->getConfig('dbPassword')
            );

        } catch (PDOException $e) {
            http_response_code(500);
            exit($e->getMessage());
        }
    }

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function closeConnection() {
        $this->connection = null;
    }
}