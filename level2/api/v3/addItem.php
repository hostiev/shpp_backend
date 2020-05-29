<?php
include '../utilFunctions.php';

session_start();

$database = connect_to_DB();
$user = get_session_user();
$body = get_input_body();
$text = filter_var($body['text'], FILTER_SANITIZE_STRING);
$id = add_item($database, $user, $text);

// Echoing id of the added item
echo json_encode(array("id" => $id));

