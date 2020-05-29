<?php
// To avoid CORS error
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    include '../headersConfig.php';
    exit();
}

include '../utilFunctions.php';

/** Time for cookie to expire in seconds */
define('COOKIE_EXPIRE_TIME', 3600 * 24 * 7); // 1 hour * 24 * 7 = 7 days

session_start();

$database = connect_to_DB();
$body = get_input_body();
$login = filter_var(trim($body['login']), FILTER_SANITIZE_STRING);
$password = filter_var(trim($body['pass']), FILTER_SANITIZE_STRING);
$userInfo = search_user_info($database, $login);
$result = array();

// If user is found and passwords are the same
if (count($userInfo) > 0 && password_verify($password, $userInfo[0]['password'])){
    $sessionID = session_id();
    // Storing session id in cookie
    setcookie('SID', $sessionID.':'.$userInfo[0]['id'], time() + COOKIE_EXPIRE_TIME);
    change_session_hash($database, $login, password_hash($sessionID, PASSWORD_DEFAULT));

    // Forming response
    $result = array(
        'code' => 200,
        'status' => 'ok',
        'message' => ''
    );

} else {
    $result = array(
        'code' => 400,
        'status' => 'error',
        'message' => 'the login or password is wrong'
    );
}

if ($result['status'] === 'ok') {
    echo json_encode(array('ok' => true));
} else {
    http_response_code($result['code']);
    echo json_encode(array($result['status'] => $result['message']));
}



