<?php
/**
 * Reads json data from php://input and returns its associative array representation.
 *
 * @return mixed|null
 * Returns associative array with data. In case of reading fail forms an error
 * response and returns null.
 */
function get_input_body() {
    $rawData = file_get_contents('php://input');
    if ($rawData === false) {
        http_response_code(500);
        echo json_encode(array('error' => 'failed to read request body'));
        return null;
    }

    return json_decode($rawData, true);
}

/**
 * Reads data from file.
 *
 * @param $filePath
 * The file path
 * @return false|string|null
 * Returns data from file. In case of reading fail forms an error response and
 * returns null.
 */
function get_data_from_file($filePath) {
    $data = file_get_contents($filePath);
    if ($data === false) {
        http_response_code(500);
        echo json_encode(array('error' => 'failed to read file'));
        return null;
    }

    return $data;
}

/**
 * Writes an associative array of items to json file.
 *
 * @param $filePath
 * The file path.
 * @param $items
 * The associative array with items.
 */
function write_items_to_file($filePath, $items) {
    $itemsToWrite = array("items" => $items);
    $itemsToWrite = json_encode($itemsToWrite);
    file_put_contents($filePath, $itemsToWrite);
}