<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__."/functions/config.php";
require_once __DIR__."/functions/helper.php";

install();

function install()
{
    $clientId = config("amo")["client_id"];
    $clientSecret = config("amo")["client_sercret"];
    $redirectUri = config("amo")["redirect_uri"];
    $authCode = $_GET["code"] ?? null;
    $subdomain = config("amo")["subdomain"];

    if (!$authCode)
    {
        $authUrl = "https://{$subdomain}.amocrm.ru/oauth2/access_token";
        $authRequestUrl = "https://{$subdomain}.amocrm.ru/oauth?client_id={$clientId}&mode=post_message&redirect_uri={$redirectUri}";
        header("Location: {$authRequestUrl}");
        exit;
    }

    $tokenUrl = "https://{$subdomain}.amocrm.ru/oauth2/access_token";

    $data = [
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'grant_type' => 'authorization_code',
        'code' => $authCode,
        'redirect_uri' => $redirectUri,
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($tokenUrl, false, $context);

    if ($response === false) {
        die('Error getting token');
    }

    file_put_contents(__DIR__."/tokens.json", $response);

    return true;

}
