<?php

namespace database;

/**
 * Manages database migrations.
 */
class Migrations
{
    const MIGRATIONS_FOLDER = 'migrations/';

    private function __construct() {}

    /**
     * Gets array of migrations, that hasn't been done.
     * @param $connection
     * @return array|false
     */
    private static function getMigrationFiles($connection)
    {
        // Getting files from directory
        $allFiles = glob(str_replace('\\', '/', realpath(dirname(__FILE__))
                . '/migrations/') . '*.sql');

        // Getting migrations, that has been done already
        $versionsFiles = [];
        $statement = $connection->query("
        SELECT `name`
        FROM `migrations`
        ");

        if (!$statement) {
            return $allFiles;
        }

        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($data as $row) {
            array_push($versionsFiles, self::MIGRATIONS_FOLDER . $row['name']);
        }

        // Returning difference
        return array_diff($allFiles, $versionsFiles);
    }

    /**
     * Migrates db with the specified file.
     * @param $connection
     * @param $filePath
     */
    private static function migrate($connection, $filePath)
    {
        $query = file_get_contents($filePath);
        $connection->query($query);
        $name = basename($filePath);
        $statement = $connection->prepare("
        INSERT INTO `migrations` (`name`)
        VALUES (:name)
        ");
        $statement->execute([':name' => $name]);
    }

    /**
     * Runs db migrations.
     */
    public static function run()
    {
        $connection = Connection::getInstance()->getConnection();
        $files = self::getMigrationFiles($connection);
        if (!empty($files)) {
            foreach ($files as $file) {
                self::migrate($connection, $file);
            }
        }
    }
}