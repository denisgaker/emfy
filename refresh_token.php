<?php

/**
 * Здесь для запросов к amoCRM буду использовать cURL (тоже умею =) )
 *
 * Функция обновления токенов запускается каждые 6 часов через планировщик CRON
 */

require_once __DIR__."/functions/config.php";
require_once __DIR__."/functions/helper.php";
require_once __DIR__."/functions/amo.php";


refreshToken();

/**
 * @return void
 */
function refreshToken()
{
    $data = [
        "client_id" => config("amo")["client_id"],
        "client_secret" => config("amo")["client_sercret"],
        "grant_type" => "refresh_token",
        "refresh_token" => json_decode(file_get_contents(__DIR__."/tokens.json"), 1)["refresh_token"],
        "redirect_uri" => config("amo")["redirect_uri"],
    ];

    $url = "https://".config("amo")["subdomain"].".amocrm.ru/oauth2/access_token";

    $req_new_token = post_request_refresh_token($url, $data);

    file_put_contents(__DIR__."/tokens.json", json_encode($req_new_token));

}