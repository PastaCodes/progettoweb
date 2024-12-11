<?php
function get_file_if_exists(string $file) : ?string {
    return file_exists($_SERVER['DOCUMENT_ROOT'] . '/IsiFitGems/' . $file) ? $file : null;
}

function get_thumbnail_if_exists(string $code) : ?string {
    return get_file_if_exists('assets/thumbnails/' . $code . '.png');
}
?>
