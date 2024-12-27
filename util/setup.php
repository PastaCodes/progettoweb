<?php
declare(strict_types = 1);
const SETTINGS = [
    'hosted-locally' => true,
    'theme-color' => '#FFFFFF' // TODO
];
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'] . (SETTINGS['hosted-locally'] ? '/IsiFitGems/' : '/'));
require __DIR__ . '/../classes/Page.php';
$page = new Page();
$page->stylesheets = ['style/pico.classless.min.css', 'style/theme_switcher.css'];
require __DIR__ . '/../classes/Script.php';
$page->scripts = [Script::external('scripts/theme_switcher.js')];
$page->prefetch = [];
?>
