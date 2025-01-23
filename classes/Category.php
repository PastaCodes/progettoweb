<?php
require_once __DIR__ . "/../util/db.php";

class Category {
    public static function fetch_all(): array {
        global $database;
        return $database->find(table: 'category');
    }
}
?>
