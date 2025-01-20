<?php
ob_start();
require_once __DIR__ . '/../' . $page->body;
$body = ob_get_clean();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="application-name" content="IsiFitGems">
        <meta name="description" content="Fashion accessories for Unibo students in Cesena.">
        <meta name="keywords" content="fashion, accessories, Unibo, Cesena, shop, e-commerce">
        <meta name="author" content="Luca Palazzini, Marco Buda">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="<?= SETTINGS['theme-color'] ?>">
<?php if ($page->allow_indexing): ?>
        <meta name="robots" content="index, follow">
<?php else: ?>
        <meta name="robots" content="none">
<?php endif ?>
        <base href="<?=
            (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') .
            $_SERVER['HTTP_HOST'] .
            (SETTINGS['hosted-locally'] ? '/IsiFitGems/' : '/')
        ?>" target="_self">
<?php foreach ($page->stylesheets as $stylesheet): ?>
        <link rel="stylesheet" type="text/css" href="<?= $stylesheet ?>">
<?php endforeach ?>
        <link rel="icon" type="image/x-icon" href="assets/isi.svg">
<?php foreach ($page->scripts as $script): ?>
        <?= $script->to_script_tag() ?>
<?php endforeach ?>
<?php foreach ($page->prefetch as $resource): ?>
        <link rel="prefetch" href="<?= $resource ?>">
<?php endforeach ?>
        <title><?= $page->title ?></title>
    </head>
    <body>
<?php if ($page->has_navbar): ?>
        <header>
            <nav><ul>
                <li><a href="">Homepage</a></li>
                <li><a href="">Products</a></li>
                <li><a href="cart">Carrello</a></li>
            </ul></nav>
        </header>
<?php endif ?>
        <button id="theme-switcher" title="Switch to dark theme">
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                <use href="assets/lightmode.svg#lightmode"></use>
            </svg>
        </button>
<?= $body ?>
<?php if ($page->has_feet): ?>
        <footer>
            <p>Footer bellissimo</p>
            <a href="">Assistenza</a>
        </footer>
<?php endif ?>
    </body>
</html>
