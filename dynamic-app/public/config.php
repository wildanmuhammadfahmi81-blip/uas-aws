<?php
declare(strict_types=1);

define('APP_NAME', getenv('APP_NAME') ?: 'UAS Administrasi Server');

function db(): mysqli
{
    static $connection = null;

    if ($connection instanceof mysqli) {
        return $connection;
    }

    $host = getenv('DB_HOST') ?: 'mariadb';
    $port = (int) (getenv('DB_PORT') ?: 3306);
    $user = getenv('DB_USER') ?: 'uas_user';
    $password = getenv('DB_PASSWORD') ?: 'uas_password';
    $database = getenv('DB_NAME') ?: 'uas_db';

    $connection = new mysqli($host, $user, $password, $database, $port);

    if ($connection->connect_error) {
        http_response_code(500);
        die('Database connection failed.');
    }

    $connection->set_charset('utf8mb4');

    return $connection;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
