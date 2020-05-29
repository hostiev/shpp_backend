<?php
// To avoid CORS error
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    include '../headersConfig.php';
    exit();
}

include '../utilFunctions.php';

$database = connect_to_DB();
$body = get_input_body();
$login = filter_var(trim($body['login']), FILTER_SANITIZE_STRING);
$password = filter_var(trim($body['pass']), FILTER_SANITIZE_STRING);
$password = password_hash($password , PASSWORD_DEFAULT);

$result = add_user($database, $login, $password);

if ($result['status'] === 'ok') {
    echo json_encode(array('ok' => true));
} else {
    http_response_code($result['code']);
    echo json_encode(array($result['status'] => $result['message']));
}