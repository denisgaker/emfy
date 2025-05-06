<?php

function config(string $type)
{
    switch ($type)
    {
        case "db":
            return [
                "user" => "cg35923_emty",
                "db" => "cg35923_emty",
                "password" => "123",
            ];
            break;

        case "amo":
            return [
                "client_id" => "d02a7525-1e6a-43f0-844c-6ad43dbf8052",
                "client_sercret" => "iiWRzsHD26q1APhzhGE14Ip7WYx49SSILpyzC00SoFoSYHv6msdMW5JLu2BzZh9k",
                "redirect_uri" => "https://denisgaker.ru/emfy/install.php",
                "subdomain" => "emtydenis"
            ];
            break;

        default:
            break;
    }
}
