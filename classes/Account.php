<?php 
require_once __DIR__ . '/../util/db.php';

class Account {
    public static function add_to_db(string $username, string $password) {
        global $database;
        $database->insert(
            table: 'account',
            data: [
                'username' => $username,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT)
            ]
        );
    }
    
    public static function check_login(string $username, string $password): bool {
        global $database;
        $account = $database->find_one(
            table: 'account',
            filters: ['username' => $username]
        );
        if (password_verify($password, $account['password_hash'])) {
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
