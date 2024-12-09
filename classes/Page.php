<?php
class Page {
    public string $title;
    public string $body;
    public array $scripts;
    public array $settings;

    function __construct(string $title, string $body, array $scripts = [], bool $allow_indexing = false) {
        $this->title = $title;
        $this->body = $body;
        $this->scripts = $scripts;
        $this->settings = [
            'allow-indexing' => $allow_indexing
        ];
    }
}
?>