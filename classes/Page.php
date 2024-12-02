<?php
class Page {
    public string $title;
    public string $body;
    public array $settings;

    function __construct(string $title, string $body, bool $allowIndexing = false) {
        $this->title = $title;
        $this->body = $body;
        $this->settings = [
            'allow-indexing' => $allowIndexing
        ];
    }
}
?>