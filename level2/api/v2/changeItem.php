<?php
include '../headersConfig.php';
include '../utilFunctions.php';

session_start();

$database = connect_to_DB();
$body = get_input_body();
$id = $body['id'];
$text = filter_var($body['text'], FILTER_SANITIZE_STRING);
$checked = $body['checked'];

if (change_item($database, $id, $text, $checked)) {
    echo json_encode(array('ok' => true));

} else {
    http_response_code(500);
    echo json_encode(array('error' => 'item update failed'));
}