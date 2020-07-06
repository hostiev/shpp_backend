<?php


namespace app\core;

/**
 * Manages configuration info.
 */
class Config
{
    private static $instance = null;
    private $data = [];

    private function __construct() {
        $this->data = include '../config/config.php';
    }

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Gets config value.
     * @param $configName
     * The specified config name.
     * @return mixed|null
     * Returns config value or null if there's no such config.
     */
    public function getConfig($configName) {
        return array_key_exists($configName, $this->data) ? $this->data[$configName] : null;
    }
}