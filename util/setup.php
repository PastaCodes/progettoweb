<?php
declare(strict_types = 1);

// Handle session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../classes/Page.php';
require_once __DIR__ . '/../classes/Script.php';
require_once __DIR__ . '/files.php';
require_once __DIR__ . '/settings.php';
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'] . (SETTINGS['hosted-locally'] ? '/IsiFitGems/' : '/'));
$base_url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . (SETTINGS['hosted-locally'] ? '/IsiFitGems/' : '/');
$url_rel = substr($_SERVER['REQUEST_URI'], strlen(SETTINGS['hosted-locally'] ? '/IsiFitGems/' : '/'));
$page = new Page();
$page->stylesheets = ['style/pico.classless.min.css', 'style/global.css'];
$page->scripts = [Script::external('scripts/global.js', 'module')];
$page->prefetch = [];
add_file_if_exists(get_directory() . 'style.css', $page->stylesheets);
add_file_if_exists(get_directory() . 'script.js', $page->scripts, fn($file) => Script::external($file, 'module'));
?>
