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

//-- task3 -------------------------------------------------------------------------------
/**
 * Outputs the HTTP response.
 */
function outputHttpResponse($statuscode, $statusmessage, $headers, $body) {
    echo 'HTTP/1.1 '.$statuscode.' '.$statusmessage."\n".$headers."\n".$body;
}

/**
 * Processes the HTTP request, which is specified in the task.
 */
function processHttpRequest($method, $uri, $headers, $body) {
    $getMethod = 'GET';
    $sumInstruction = '/sum';
    $numsList = '?nums=';
    $statusCode = '';
    $statusMessage = '';
    $sum = 'not found';

    if (strcmp($method, $getMethod) != 0 || strpos($uri, $numsList) === false) {
        $statusCode = '400';
        $statusMessage = 'Bad Request';
    }

    if (strpos($uri, $sumInstruction) === false) {
        $statusCode = '404';
        $statusMessage = 'Not Found';
    }
    // Counting sum if request is correct
    if (strlen($statusCode) == 0) {
        $statusCode = '200';
        $statusMessage = 'OK';
        $numbers = explode("=", $uri);
        $numbers = explode(",", $numbers[1]);

        $sum = 0;
        for ($i = 0; $i < count($numbers); $i++) {
            $sum += $numbers[$i];
        }
    }

    $contentLength = strlen($sum);
    $headers = <<<END
Server: Apache/2.2.14 (Win32)
Connection: Closed
Content-Type: text/html; charset=utf-8
Content-Length: $contentLength
END;

    outputHttpResponse($statusCode, $statusMessage, $headers, $sum);
}
//----------------------------------------------------------------------------------------

$contents = readHttpLikeInput();
$http = parseTcpStringAsHttpRequest($contents);
processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);