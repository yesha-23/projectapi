<?php
function getDb(): PDO {
    static $db = null;
    if ($db !== null) {
        return $db;
    }

    $host = '127.0.0.1';
    $user = 'root';
    $pass = '';
    $dbname = 'rekammedis';

    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $db;
}