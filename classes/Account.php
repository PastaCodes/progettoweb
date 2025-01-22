<?php 
require_once __DIR__ . '/../util/db.php';

class Account {
    public static function check_login(string $username, string $password): bool {
        global $database;
        $account = $database->find_one(
            table: 'account',
            filters: ['username' => $username]
        );
        if (password_verify($password, $account['password'])) {
            return true;
        }
        return false;
    }

    public static function check_exists(string $username): bool {
        global $database;
        $account = $database->find_one(
            table: 'account',
            filters: ['username' => $username]
        );
        return $account ? true : false;
    }
};
?>
