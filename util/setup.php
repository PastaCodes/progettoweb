<?php
declare(strict_types = 1);

ini_set('session.cookie_secure', 1); // Forces secure cookie transmission
ini_set('session.cookie_httponly', 1); // Prevents access to session cookies via JavaScript

if (!isset($_SESSION['username'])) {
    session_start();
}

require_once __DIR__ . '/../classes/Page.php';
require_once __DIR__ . '/../classes/Script.php';
require_once __DIR__ . '/files.php';

const SETTINGS = [
    'hosted-locally' => true,
    'theme-color' => '#FFFFFF' // TODO
];
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'] . (SETTINGS['hosted-locally'] ? '/IsiFitGems/' : '/'));
$page = new Page();
$page->scripts = [Script::external('scripts/global.js', 'module')];
$page->stylesheets = ['style/pico.classless.min.css', 'style/global.css'];
$page->scripts = [Script::external('scripts/global.js', 'module')];
$page->prefetch = [];
add_file_if_exists(get_directory() . 'style.css', $page->stylesheets);
add_file_if_exists(get_directory() . 'script.js', $page->scripts, fn($file) => Script::external($file, 'module'));
?>
