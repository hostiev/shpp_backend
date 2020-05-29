<?php
include '../utilFunctions.php';

session_start();
$database = connect_to_DB();
$user = get_session_user();

echo json_encode(get_items_by_user($database, $user));
