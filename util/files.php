<?php
function get_directory() : string {
    return substr(getcwd() . '/', strlen(DOCUMENT_ROOT));
}

function file_exists_rel(string $file): bool {
    return file_exists(DOCUMENT_ROOT . $file);
}

function add_file_if_exists(string $file, array &$files, ?callable $map = null) {
    if (file_exists_rel($file))
        $files[] = $map ? $map($file) : $file;
}

function get_file_if_exists(string $file) : ?string {
    return file_exists_rel($file) ? $file : null;
}

function read_file(string $file) : string {
    return file_get_contents(DOCUMENT_ROOT . $file);
}
?>
