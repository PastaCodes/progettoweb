<?php
require_once __DIR__ . "/../classes/Database.php";

if (SETTINGS['hosted-locally']) {
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'isifitgems';
} else {
    // Not implemented
}

$database = new Database($servername, $username, $password, $dbname);
