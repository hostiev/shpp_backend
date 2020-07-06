<?php


namespace app\models;


use app\core\Model;
use PDO;

/**
 * Manages user data.
 */
class UsersModel extends Model {

    /**
     * Gets the data of the specified user.
     * @param $userName
     * @return mixed
     */
    public function getUserData($userName) {
        $statement = $this->connection->prepare("
            SELECT `name`, `password`
            FROM `users`
            WHERE `name` = :userName
        ");
        $statement->execute(['userName' => $userName]);

        return $statement->fetchAll(PDO::FETCH_ASSOC)[0];
    }
}