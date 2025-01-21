<?php
require_once __DIR__ . '/Database.php';

class Notification {
    public string $title;
    public string $description;
    public DateTimeInterface $created_at;
    public bool $seen;

    public function __construct(string $title, string $desc, DateTimeInterface $timestamp, bool $seen) {
        $this->title = $title;
        $this->description = $desc;
        $this->created_at = $timestamp;
        $this->seen = $seen;
    }

    public static function fetch_all(): array {
        global $database;
        $notification_result = $database->find(
            table: 'notification',
            options: ['order_by' => ['created_at' => 'DESC']]
        );
        $notifications = [];
        foreach ($notification_result as $notification) {
            $notifications[] = new Notification($notification['title'], $notification['description'], 
                new DateTime($notification['created_at']), $notification['is_read']);
        }
        return $notifications;
    }
}
?>
