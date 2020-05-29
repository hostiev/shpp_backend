<?php
include '../headersConfig.php';
include 'dataFunctions.php';

// Getting input data
$body = get_input_body();
$id = $body['id'];
$text = filter_var($body['text'], FILTER_SANITIZE_STRING);
$checked = $body['checked'];

// Getting stored items
$itemsData = get_data_from_file('items.json');
$items = json_decode($itemsData, true);
$items = $items['items'];

// Searching and changing item by id
for ($i = 0; $i < count($items); $i++) {
    if ($items[$i]['id'] == $id) {
        $items[$i]['text'] = $text;
        $items[$i]['checked'] = $checked;
        break;
    }
}

write_items_to_file('items.json', $items);

echo json_encode(array("ok" => true));