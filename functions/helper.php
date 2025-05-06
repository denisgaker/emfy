<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



/**
 * Подключение к базе данных
 *
 * @return mysqli|void
 */
function getMySQLi()
{
    $db_user = config("db")["user"];
    $db_name = config("db")["db"];
    $db_password = config("db")["password"];
    $mysqli = new mysqli("localhost", $db_user, $db_password, $db_name);


    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        saveLog("Ошибка подключения к БД", ["error" => $mysqli->connect_error]);
        exit();
    }
    $mysqli->select_db($db_name);

    mysqli_query($mysqli, "SET NAMES 'utf8'");
    mysqli_query($mysqli, "SET collation_connection = 'utf8_unicode_ci'");

    return $mysqli;
}


/**
 * Отладка
 *
 * @param string|int|bool|array|object $a
 * @param string $title
 * @param int $die
 * @return void
 */
function vd(string|int|bool|array|object $a, string $title = "", int $die = 1)
{
    echo "<pre>";

    if ($title !== "")
        echo $title . "<br />===========<br />";

    var_dump($a);

    if ($die === 1)
        die("Остановка сценария");
    else
        echo "<hr />";

    echo "</pre>";

}


/**
 * Сохранение логов
 *
 * @param string $mes
 * @param array $data
 * @return bool
 */
function saveLog(string $mes, array $data)
{
    file_put_contents(
        __DIR__ . "/logs/log_" . date("d-m-Y") . ".log",
        date("d.m.Y H:i:s") . " | " . $mes . ": " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n",
        FILE_APPEND,
    );

    return true;
}

