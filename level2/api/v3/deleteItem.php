<?php
include '../utilFunctions.php';

session_start();

$database = connect_to_DB();
$body = get_input_body();
$id = $body['id'];

if (delete_item($database, $id)) {
    echo json_encode(array('ok' => true));

} else {
    http_response_code(500);
    echo json_encode(array('error' => 'item delete failed'));
}