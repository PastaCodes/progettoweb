<?php
class Page {
    public string $title;
    public string $body;
    public array $scripts;
    // Page settings
    public bool $allow_indexing;
    public bool $has_navbar;
    public bool $has_feet;

    function __construct(string $title, string $body, array $scripts = [], bool $allow_indexing = false, bool $has_navbar = false, bool $has_feet = false) {
        $this->title = $title;
        $this->body = $body;
        $this->scripts = $scripts;
        $this->allow_indexing = $allow_indexing;
        $this->has_navbar = $has_navbar;
        $this->has_feet = $has_feet;
    }
}
?>
