<?php
declare(strict_types = 1);

require_once __DIR__ . '/../classes/Page.php';
require_once __DIR__ . '/../classes/Script.php';
require_once __DIR__ . '/files.php';

const SETTINGS = [
    'hosted-locally' => true,
    'theme-color' => '#FFFFFF' // TODO
];
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'] . (SETTINGS['hosted-locally'] ? '/IsiFitGems/' : '/'));
$page = new Page();
$page->stylesheets = ['style/pico.classless.min.css', 'style/global.css'];
add_file_if_exists(get_directory() . 'style.css', $page->stylesheets);
$page->scripts = [Script::external('scripts/global.js', 'module')];
// FIXME: Temporary fix
if (file_exists_rel('./' . get_directory() . 'script.js')) {
    $page->scripts[] = Script::external(get_directory() . 'script.js', 'module');
}
// add_file_if_exists(get_directory() . 'script.js', $page->scripts, 'Script::external');
$page->prefetch = [];
?>
