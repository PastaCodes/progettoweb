<?php
function get_file_if_exists(string $file) : ?string {
    return file_exists(DOCUMENT_ROOT . $file) ? $file : null;
}

function get_thumbnail_if_exists(string $code) : ?string {
    return get_file_if_exists('assets/thumbnails/' . $code . '.png');
}

function read_file(string $file) : string {
    return file_get_contents(DOCUMENT_ROOT . $file);
}
?>
