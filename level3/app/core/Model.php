<?php


namespace app\core;


use database\Connection;

/**
 * An abstract model class.
 */
abstract class Model {
    protected $connection;

    /**
     * Constructs the model with reference to the PDO connection.
     */
    public function __construct() {
        $this->connection = Connection::getInstance()->getConnection();
    }
}