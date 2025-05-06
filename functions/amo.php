<?php

function post_request($method, $data)
{
    $subdomain = config("amo")["subdomain"];
    $url = "https://".$subdomain.".amocrm.ru/api/v4/$method";
    vd($url, '$url', 0);
    $token = json_decode(file_get_contents(dirname(__DIR__, 1)."/tokens.json"), 1)["access_token"];

    $headers = [
        "Authorization: Bearer ".$token,
    ];

    vd([
        "data" => $data,
        "headers" => $headers,
        "url" => $url,
    ], "Данные для запроса в amoCRM", 0);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    curl_close($curl);



    return json_decode($response, 1);
}

function get_request($method, $data=null)
{
    $subdomain = config("amo")["subdomain"];
    $url = "https://{$subdomain}.amocrm.ru/api/v4/{$method}";
    if ($data != null) $url .= "?".json_encode($data);
    $token = json_decode(file_get_contents(dirname(__DIR__, 1)."/tokens.json"), 1)["access_token"];

    $headers = [
        "Authorization: Bearer ".$token,
    ];

    $ch = curl_init();


    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if ($response === false) {
        die('Error occurred while fetching the data: '
            . curl_error($ch));
    }

    curl_close($ch);

    $response = json_decode($response, 1);

    return $response;
}

/**
 * @param $url
 * @param $data
 * @return mixed|void
 */
function post_request_refresh_token($url, $data)
{
    $curl = curl_init();
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
    curl_setopt($curl,CURLOPT_URL, $url);
    curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
    curl_setopt($curl,CURLOPT_HEADER, false);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
    $out = curl_exec($curl);
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $code = (int)$code;
    $errors = [
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable',
    ];

    try
    {
        if ($code < 200 || $code > 204) {
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
        }
    }
    catch(\Exception $e)
    {
        die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
    }

    $response = json_decode($out, true);

    return $response;
}