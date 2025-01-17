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
$page->scripts = [Script::external(src: 'scripts/theme_switcher.js')];
$page->prefetch = [];
add_file_if_exists(get_directory() . 'style.css', $page->stylesheets);
add_file_if_exists(get_directory() . 'script.js', $page->scripts, 'Script::external');
?>
