<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'freelanci');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

require_once __DIR__ . '/../bootstrap.php';

function getDBConnection(): PDO
{
    return Database::getInstance()->getConnection();
}

function isDatabaseConnected(): bool
{
    return Database::isConnected();
}
