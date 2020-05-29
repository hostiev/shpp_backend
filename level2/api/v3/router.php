<?php
include_once '../headersConfig.php';

// Routing according to action parameter
switch ($_REQUEST['action']) {
    case 'addItem':
        include 'addItem.php';
        break;

    case 'getItems':
        include 'getItems.php';
        break;

    case 'changeItem':
        include 'changeItem.php';
        break;

    case 'deleteItem':
        include 'deleteItem.php';
        break;

    case 'login':
        include '../authorization/login.php';
        break;

    case 'logout':
        include '../authorization/logout.php';
        break;

    case 'register':
        include '../authorization/register.php';
        break;

    // Forming an error response
    default:
        http_response_code(400);
        echo json_encode(array('error' => 'bad request'));
}


