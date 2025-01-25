<?php
class Page {
    public string $title;
    public string $body;
    public bool $has_navbar = false;
    public bool $has_feet = false;
    public bool $track_page_cookie = true;
    public array $stylesheets;
    public array $scripts;
    public array $prefetch;
    public bool $allow_indexing = false;
}
?>
