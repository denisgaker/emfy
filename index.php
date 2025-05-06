<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__."/functions/config.php";
require_once __DIR__."/functions/helper.php";
require_once __DIR__."/functions/amo.php";

get_req();

function get_req()
{
    $req = $_REQUEST;
    $time = time();
    file_put_contents(__DIR__."/tmp/req_{$time}_.json", json_encode($req));
//die();
//    $req = json_decode(file_get_contents(__DIR__."/tmp/req_1746537954_.json"), 1);
//    vd($req, '$req', 1);

    switch (true)
    {
        case isset($req["leads"]["add"]):
            lead_add($req);
            break;

        case isset($req["leads"]["update"]):
            lead_update($req);
            break;

        case isset($req["contacts"]["add"]):
            contacts_add($req);
            break;

        case isset($req["contacts"]["update"]):
            contacts_update($req);
            break;

        default:
            break;
    }
}

function contacts_update($req)
{
    $contact = $req["contacts"]["update"][0];

    $data = [[
        "filter" => [
            "entity" => ["contact"],
            "entity_id" => [$contact["id"]],
        ]
    ]];

    $news = "";

    $event = get_request("events", $data);

    foreach ($event["_embedded"]["events"] as $e)
    {
        if ((int)$e["created_at"] === (int)$contact["last_modified"])
        {
            vd($e["value_after"], 'e', 0);

            if (isset($e["value_after"][0]["sale_field_value"]))
                $news .= "Бюджет = ".$e["value_after"][0]["sale_field_value"]["sale"]."\n";

            if (isset($e["value_after"][0]["custom_field_value"]))
            {
                $field = get_request("contacts/custom_fields/".$e["value_after"][0]["custom_field_value"]["field_id"])["name"];
                $news .= $field." = ".$e["value_after"][0]["custom_field_value"]["text"]."\n";
            }
        }
    }

    $title = $contact["name"];
    $id = $contact["id"];
    $responsible_user_id = $contact["responsible_user_id"];
    $date_update = date("d-m-Y H:i:s", (int)$contact["updated_at"]);

    $user = get_request("users/{$responsible_user_id}");

    $responsible_user_name = (isset($user["name"])) ? "Ответственный пользователь: ".$user["name"] : "Не удалось поределить ответственного пользователя";

    $text = "Изменен Контакт\nНазвание: {$title}\n{$responsible_user_name}\nВремя изменения: {$date_update}\n\nНовые поля\n{$news}";

    $data = [[
        "entity_id" => (int)$id,
        "note_type" => "common",
        "responsible_user_id" => (int)$responsible_user_id,
        "params" => [
            "text" => $text
        ]
    ]];
    $add_note = post_request("contacts/notes", $data);
}

function lead_update($req)
{
    $lead = $req["leads"]["update"][0];

    $data = [[
        "filter" => [
            "entity" => ["lead"],
            "entity_id" => [$lead["id"]],
        ]
    ]];

    $news = "";

    $event = get_request("events", $data);

    foreach ($event["_embedded"]["events"] as $e)
    {
        if ((int)$e["created_at"] === (int)$lead["last_modified"])
        {
            vd($e["value_after"], 'e', 0);

            if (isset($e["value_after"][0]["sale_field_value"]))
                $news .= "Бюджет = ".$e["value_after"][0]["sale_field_value"]["sale"]."\n";

            if (isset($e["value_after"][0]["custom_field_value"]))
            {
                $field = get_request("leads/custom_fields/".$e["value_after"][0]["custom_field_value"]["field_id"])["name"];
                $news .= $field." = ".$e["value_after"][0]["custom_field_value"]["text"]."\n";
            }
        }
    }

    $title = $lead["name"];
    $id = $lead["id"];
    $responsible_user_id = $lead["responsible_user_id"];
    $date_update = date("d-m-Y H:i:s", (int)$lead["updated_at"]);

    $user = get_request("users/{$responsible_user_id}");

    $responsible_user_name = (isset($user["name"])) ? "Ответственный пользователь: ".$user["name"] : "Не удалось поределить ответственного пользователя";

    $text = "Изменена сделка\nНазвание: {$title}\n{$responsible_user_name}\nВремя изменения: {$date_update}\n\nНовые поля\n{$news}";

    $data = [[
        "entity_id" => (int)$id,
        "note_type" => "common",
        "responsible_user_id" => (int)$responsible_user_id,
        "params" => [
            "text" => $text
        ]
    ]];
    $add_note = post_request("leads/notes", $data);
}


function contacts_add($req)
{
    $name = $req["contacts"]["add"][0]["name"];
    $id = $req["contacts"]["add"][0]["id"];
    $responsible_user_id = $req["contacts"]["add"][0]["responsible_user_id"];
    $date_create = date("d-m-Y H:i:s", (int)$req["contacts"]["add"][0]["date_create"]);

    $user = get_request("users/{$responsible_user_id}");

    $responsible_user_name = (isset($user["name"])) ? "Ответственный пользователь: ".$user["name"] : "Не удалось поределить ответственного пользователя";

    $text = "Добавлен контакт\nНазвание: {$name}\n{$responsible_user_name}\nВремя создания: {$date_create}\n";

    $data = [[
        "entity_id" => (int)$id,
        "note_type" => "common",
        "responsible_user_id" => (int)$responsible_user_id,
        "params" => [
            "text" => $text
        ]
    ]];
    $add_note = post_request("contacts/notes", $data);
    vd($add_note, '$add_note', 0);
}

function lead_add($req)
{

    $title = $req["leads"]["add"][0]["name"];
    $id = $req["leads"]["add"][0]["id"];
    $responsible_user_id = $req["leads"]["add"][0]["responsible_user_id"];
    $date_create = date("d-m-Y H:i:s", (int)$req["leads"]["add"][0]["date_create"]);

    $user = get_request("users/{$responsible_user_id}");

    $responsible_user_name = (isset($user["name"])) ? "Ответственный пользователь: ".$user["name"] : "Не удалось поределить ответственного пользователя";

    $text = "Добавлена сделка\nНазвание: {$title}\n{$responsible_user_name}\nВремя создания: {$date_create}\n";

    $data = [[
        "entity_id" => (int)$id,
        "note_type" => "common",
        "responsible_user_id" => (int)$responsible_user_id,
        "params" => [
            "text" => $text
        ]
    ]];
    $add_note = post_request("leads/notes", $data);
}
