<?php
declare(strict_types = 1);
const SETTINGS = [
    'hosted-locally' => true,
    'theme-color' => '#FFFFFF' // TODO
];
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'] . (SETTINGS['hosted-locally'] ? '/IsiFitGems/' : '/'));
$prefetch = [];
require 'classes/Page.php';
?>
