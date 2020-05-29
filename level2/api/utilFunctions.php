<?php
include_once 'DBFunctions.php';

/**
 * Gets user's login from stored cookies.
 *
 * @return mixed|null
 * Returns user's login. In case of failure forms an error response and returns null.
 */
function get_session_user() {
    // If there're no cookies
    if (!isset($_COOKIE['SID'])) {
        http_response_code(401);
        echo json_encode(array('error' => 'the requested page needs a username and a password'));
        return null;
    }

    // Getting user id and session id
    $cookie = explode(':', $_COOKIE['SID']);
    $sessionID = $cookie[0];
    $userID = $cookie[1];

    // Searching user and corresponding session id hash by id
    $database = connect_to_DB();
    $rows = search_user_by_id($database, $userID);

    // If the user is found and session id's are the same
    if (count($rows) > 0 && password_verify($sessionID, $rows[0]['session_hash'])) {
        return $rows[0]['login'];
    } else {
        http_response_code(500);
        echo json_encode(array('error' => 'failed to find user'));
        return null;
    }
}

/**
 * Reads json data from php://input and returns its associative array representation.
 *
 * @return mixed|null
 * Returns associative array with data. In case of reading fail forms an error
 * response and returns null.
 */
function get_input_body() {
    $rawData = file_get_contents("php://input");
    if ($rawData === false) {
        http_response_code(500);
        echo json_encode(array('error' => 'failed to read request body'));
        return null;
    }

    return json_decode($rawData, true);
}