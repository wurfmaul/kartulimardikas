<?php

define('BASEDIR', __DIR__ . '/../');
require_once BASEDIR . 'config/config.php';

echo "Connect to database...";
$sql = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (mysqli_connect_errno()) {
    echo "FAILED: Connection refused!" . PHP_EOL;
    exit(1);
}
$sql->query("USE " . DB_NAME . ";");
echo "DONE" . PHP_EOL;

echo "Set up structures.....";
$queries = explode(';', file_get_contents(BASEDIR . "config/database.sql"));
foreach ($queries as $query) {
    if (!empty(trim($query))) {
        if ($sql->query($query) === false) {
            echo "FAILED: " . $sql->sqlstate . PHP_EOL;
            exit(1);
        }
    };
}
echo "DONE" . PHP_EOL;