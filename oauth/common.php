<?php
function curlHelper($url, $method = 'GET', $data = null, $header = [], $cookies = "")
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    switch ($method) {
        case "GET":
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            break;
        case "POST":
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "DELETE":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "PATCH":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "TRACE":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "TRACE");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "OPTIONS":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "OPTIONS");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "HEAD":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "HEAD");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        default:
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $response = curl_exec($ch);
    $output = [];
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    // 根据头大小去获取头信息内容
    $output['header'] = substr($response, 0, $headerSize);
    $output['body'] = substr($response, $headerSize, strlen($response) - $headerSize);
    $output['detail'] = curl_getinfo($ch);
    curl_close($ch);
    return $output;
}