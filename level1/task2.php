<?php
function readHttpLikeInput() {
    $f = fopen( 'php://stdin', 'r' );
    $store = "";
    $toread = 0;
    while( $line = fgets( $f ) ) {
        $store .= preg_replace("/\r/", "", $line);
        if (preg_match('/Content-Length: (\d+)/',$line,$m))
            $toread=$m[1]*1;
        if ($line == "\r\n")
            break;
    }
    if ($toread > 0)
        $store .= fread($f, $toread);
    return $store;
}

//-- task2 -------------------------------------------------------------------------------
/**
 * Parses the specified string and forms an array, which consists of method, uri, header and body.
 *
 * @param $string
 * The specified string.
 * @return array Returns associative array with method, uri, header and body.
 */
function parseTcpStringAsHttpRequest($string) {
    $lines = explode("\n", $string);
    // Getting method and uri
    $firstLine = explode(" ", $lines[0]);
    $method = $firstLine[0];
    $uri = $firstLine[1];
    // Getting headers
    $headers = array();
    for ($i = 1, $length = count($lines); $i < $length; $i++) {
        $pos = strpos($lines[$i], ": ");
        if ($pos !== false) {
            $header = explode(": ", $lines[$i]);

            $headers[] = $header;
        }
    }
    // Getting body
    $body = "";
    $headersNumber = count($headers);
    for ($i = $headersNumber + 1, $length = count($lines); $i < $length; $i++) {
        $body .= $lines[$i];
    }

    return array(
        "method" => $method,
        "uri" => $uri,
        "headers" => $headers,
        "body" => $body,
    );
}
//----------------------------------------------------------------------------------------

$contents = readHttpLikeInput();
$http = parseTcpStringAsHttpRequest($contents);
echo(json_encode($http, JSON_PRETTY_PRINT));