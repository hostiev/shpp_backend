<?php
include '../headersConfig.php';
include 'dataFunctions.php';

// Otherwise it adds an empty item on preflight request
if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
    // Getting input data
    $body = get_input_body();
    $text = filter_var($body['text'], FILTER_SANITIZE_STRING);

    // Getting id and stored items
    $newId = get_data_from_file('id.txt');
    $itemsData = get_data_from_file('items.json');
    $items = json_decode($itemsData, true);
    $items = $items['items'];

    // Forming and adding a new item
    $items[] = array(
        "id" => $newId,
        "text" => $text,
        "checked" => false
    );

    write_items_to_file('items.json', $items);

    // Echoing id of the new item
    echo json_encode(array("id" => $newId));

    file_put_contents("id.txt", ++$newId);
}