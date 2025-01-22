<?php
require_once __DIR__ . '/Database.php';

class Notification {
    public int $id;
    public string $title;
    public string $content;
    public DateTimeInterface $created_at;

    public function __construct(int $id, string $title, string $content, DateTimeInterface $timestamp) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->created_at = $timestamp;
    }

    public static function fetch_all(): array {
        global $database;
        $notification_result = $database->find(
            table: 'notification',
            options: ['order_by' => ['created_at' => 'DESC']]
        );
        $notifications = [];
        foreach ($notification_result as $notification) {
            $notifications[] = new Notification($notification['id'], $notification['title'], $notification['content'],
                new DateTime($notification['created_at']));
        }
        return $notifications;
    }
}
?>
