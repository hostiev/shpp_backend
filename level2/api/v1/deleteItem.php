<?php
include '../headersConfig.php';
include 'dataFunctions.php';

// Getting input data
$body = get_input_body();
$id = $body['id'];

// Getting stored items
$itemsData = get_data_from_file('items.json');
$items = json_decode($itemsData, true);
$items = $items['items'];

// Searching item by id and deleting it
for ($i = 0; $i < count($items); $i++) {
    if ($items[$i]['id'] == $id) {
        array_splice($items, $i, 1);
        break;
    }
}

write_items_to_file('items.json', $items);

echo json_encode(array("ok" => true));