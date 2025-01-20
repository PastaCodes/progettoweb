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
            <a href="">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="IsiFitGems logo">
                    <use href="assets/isi.svg#isi"></use>
                </svg>
                <h1>IsiFitGems</h1>
            </a>
            <nav><ul>
                <li><a href="shop">Our products</a></li>
                <li><a href="">Bundles</a></li>
                <li><a href="">Support</a></li>
                <li><a href="">Notifications <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label=""><use href="assets/notifications.svg#notifications"></use></svg></a></li>
                <li><a href="">Your orders <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label=""><use href="assets/orders.svg#orders"></use></svg></a></li>
                <li><a href="cart">Your cart <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label=""><use href="assets/cart.svg#cart"></use></svg></a></li>
            </ul></nav>
        </header>
<?php endif ?>
        <div id="side-buttons">
            <button title="Switch to dark theme">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                    <use href="assets/lightmode.svg#lightmode"></use>
                </svg>
            </button>
            <button title="Accessibility options">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                    <use href="assets/accessibility.svg#accessibility"></use>
                </svg>
            </button>
        </div>
<?= $body ?>
<?php if ($page->has_feet): ?>
        <footer>
            <a>Terms and Conditions</a>
            <a>Privacy Policy</a>
            <p>&copy; 2025 IsiFitGems s.r.l.</p>
        </footer>
<?php endif ?>
    </body>
</html>
