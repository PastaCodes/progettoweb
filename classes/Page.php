<?php
class Page {
    public string $title;
    public string $body;
    public array $scripts;
    // Page settings
    public bool $allow_indexing;
    public bool $has_navbar;
    public bool $has_feet;

    function __construct(string $title, string $body, array $scripts = []) {
        $this->title = $title;
        $this->body = $body;
        $this->scripts = $scripts;
        $this->allow_indexing = false;
        $this->has_navbar = false;
        $this->has_feet = false;
    }
}
?>
