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

//-- task4 -------------------------------------------------------------------------------
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
    $expectedUri = '/api/checkLoginAndPassword';
    $expectedContentType = 'application/x-www-form-urlencoded';
    $statusCode = '401';
    $statusMessage = 'Unauthorized';
    $returnedBody = 'user not found';

    if (strcmp($uri, $expectedUri) != 0 || strcmp($headers['Content-Type'], $expectedContentType) != 0) {
        $statusCode = '400';
        $statusMessage = 'Bad Request';
    }

    if (strcmp($statusCode, '401')) {
        $fileContent = file_get_contents('passwords.txt');
        // If passwords.txt is missing
        if ($fileContent === false) {
            $statusCode = '500';
            $statusMessage = 'Internal Server Error';
        }

        $fileContent = explode("\n", $fileContent);
        $loginInfo = explode("&", $body);
        $userName = explode("=", $loginInfo[0])[1];
        $userPassword = explode("=", $loginInfo[1])[1];
        $loginInfo = $userName.':'.$userPassword;
        // Looking for the match and forming the body
        for ($i = 0, $length = count($fileContent); $i < $length; $i++) {
            if (strcmp($fileContent, $loginInfo) == 0) {
                $statusCode = '200';
                $statusMessage = 'OK';
                $returnedBody = '<h1 style="color:green">FOUND</h1>';
                break;
            }
        }
    }

    $contentLength = strlen($returnedBody);
    $headers = <<<END
Accept: */*
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/4.0
Content-Length: $contentLength
END;

    outputHttpResponse($statusCode, $statusMessage, $headers, $returnedBody);
}
//----------------------------------------------------------------------------------------

$contents = readHttpLikeInput();
$http = parseTcpStringAsHttpRequest($contents);
processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);