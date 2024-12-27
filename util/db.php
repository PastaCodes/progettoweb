<?php
if (SETTINGS['hosted-locally']) {
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'isifitgems';
} else {
    // Not implemented
}
$db = new mysqli($servername, $username, $password, $dbname);
?>
