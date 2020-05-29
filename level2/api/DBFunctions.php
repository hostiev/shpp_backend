<?php
include_once 'DBConfig.php';

/**
 * Connects to database and creates tables for todo's and users.
 *
 * @return PDO|null
 * Returns a PDO instance representing a connection to a database.
 * In case an exception caught in process - forms an error response
 * and returns null.
 */
function connect_to_DB() {
    try {
        $database = new PDO(DSN, USER, PASSWORD);
        create_todos_table($database);
        create_users_table($database);

        return $database;

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array('error' => $e->getMessage()));
        return null;
    }
}

/**
 * Creates a table for todo's.
 *
 * @param PDO $database
 * The PDO connection to a database.
 */
function create_todos_table(PDO $database) {
    $query = "CREATE TABLE IF NOT EXISTS `todos` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user` VARCHAR(".LOGIN_CELL_SIZE.") NOT NULL,
        `text` VARCHAR(".TEXT_CELL_SIZE.") NOT NULL,
        `checked` BOOLEAN NOT NULL DEFAULT FALSE,
        UNIQUE KEY (`id`)
    )";

    $database->exec($query);
}

/**
 * Creates a table for users.
 *
 * @param PDO $database
 * The PDO connection to a database.
 */
function create_users_table(PDO $database) {
    $query = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, 
        `login` VARCHAR(".LOGIN_CELL_SIZE.") NOT NULL, 
        `password` VARCHAR(".PASSWORD_CELL_SIZE.") NOT NULL,
        `session_hash` VARCHAR(".PASSWORD_CELL_SIZE.") NOT NULL DEFAULT '', 
        UNIQUE KEY (`id`)
    )";

    $database->exec($query);
}

/**
 * Gets items from todos table by user.
 *
 * @param PDO $database
 * The PDO connection to a database.
 * @param $user
 * The user to search by.
 * @return array
 * Returns the array with items.
 */
function get_items_by_user(PDO $database, $user) {
    $statement = $database->query("
        SELECT `id`, `text`, `checked` 
        FROM `todos` 
        WHERE `user` = '$user'
    ");
    $items = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Converting int values to boolean
    for ($i = 0; $i < count($items); $i++) {
        $items[$i]['checked'] = boolval($items[$i]['checked']);
    }

    return array('items' => $items);
}

/**
 * Changes the item by the given id in todos table.
 *
 * @param PDO $database
 * The PDO connection to a database.
 * @param $id
 * The id of the item.
 * @param $text
 * The new text.
 * @param $checked
 * The task checked status.
 * @return bool
 * Returns true on success or false on failure.
 */
function change_item(PDO $database, $id, $text, $checked) {
    $statement = $database->prepare("
        UPDATE `todos` 
        SET `text` = :text, `checked` = :checked 
        WHERE `id` = :id
    ");

    return $statement->execute(['text' => $text, 'checked' => (int) $checked, 'id' => $id]);
}

/**
 * Adds the new item to the todos table and returns the id of the added item.
 *
 * @param PDO $database
 * The PDO connection to a database.
 * @param $user
 * The user that added new task.
 * @param $text
 * The text of the new task.
 * @return mixed
 * Returns the id of added item or false on failure.
 */
function add_item(PDO $database, $user, $text) {
    $statement = $database->prepare("
        INSERT INTO `todos` (`user`, `text`, `checked`) 
        VALUES (:user, :text, :checked)
    ");
    $statement->execute(['user' => $user, 'text' => $text, 'checked' => 0]);

    $statement = $database->query("
        SELECT MAX(`id`) 
        FROM `todos`
    ");

    return $statement->fetch(PDO::FETCH_LAZY)[0];
}

/**
 * Deletes the item by id from todos table.
 *
 * @param PDO $database
 * The PDO connection to a database.
 * @param $id
 * The id of the item.
 * @return bool
 * Returns true on success or false on failure.
 */
function delete_item(PDO $database, $id) {
    $statement = $database->prepare("
        DELETE FROM `todos` 
        WHERE `id` = :id
    ");

    return $statement->execute(['id' => $id]);
}

/**
 * Adds new user to the users table.
 *
 * @param PDO $database
 * The PDO connection to a database.
 * @param $login
 * The login of the new user.
 * @param $password
 * The password of the new user.
 * @return array
 * Returns the array with formed response info.
 */
function add_user(PDO $database, $login, $password) {
    $userInfo = search_user_info($database, $login);
    if (count($userInfo) > 0) {
        return array(
            'code' => 400,
            'status' => 'error',
            'message' => 'this name is already taken: '.$login
        );
    }

    $statement = $database->prepare("
        INSERT INTO `users` (`login`, `password`) 
        VALUES (:login, :password)
    ");

    return $statement->execute(['login' => $login, 'password' => $password])
        ? array(
            'code' => 200,
            'status' => 'ok',
            'message' => 'user created successfully'
        )
        : array(
            'code' => 500,
            'status' => 'error',
            'message' => 'failed to create new user'
        );
}

/**
 * Searches info by login in the users table.
 *
 * @param PDO $database
 * The PDO connection to a database.
 * @param $login
 * The user's login.
 * @return array
 * Returns the associative array with users data.
 */
function search_user_info(PDO $database, $login) {
    $statement = $database->query("
        SELECT * 
        FROM `users` 
        WHERE `login` = '$login'
    ");

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Changes the hash of the session id in users table.
 *
 * @param PDO $database
 * The PDO connection to a database.
 * @param $login
 * The user's login.
 * @param $sessionHash
 * The session id hash.
 * @return bool
 * Returns true on success or false on failure.
 */
function change_session_hash(PDO $database, $login, $sessionHash) {
    $statement = $database->prepare("
        UPDATE `users` 
        SET `session_hash` = :sessionHash 
        WHERE `login` = :login
    ");

    return $statement->execute(['sessionHash' => $sessionHash, 'login' => $login]);
}

/**
 * Searches user by id in the users table.
 *
 * @param PDO $database
 * The PDO connection to a database.
 * @param $userID
 * The user's id.
 * @return array
 * Returns the associative array with user's login and session id hash.
 */
function search_user_by_id(PDO $database, $userID) {
    $statement = $database->query("
        SELECT `login`, `session_hash`
        FROM `users` 
        WHERE `id` = '$userID'
    ");

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}