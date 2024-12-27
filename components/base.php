<?php
ob_start();
require $page->body;
$body = ob_get_clean();
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="application-name" content="IsiFitGems" />
        <meta name="description" content="Fashion accessories for Unibo students in Cesena." />
        <meta name="keywords" content="fashion, accessories, Unibo, Cesena, shop, e-commerce" />
        <meta name="author" content="Luca Palazzini, Marco Buda" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="<?= SETTINGS['theme-color'] ?>" />
<?php if ($page->allow_indexing): ?>
        <meta name="robots" content="index, follow" />
<?php else: ?>
        <meta name="robots" content="none" />
<?php endif ?>
        <base href="<?=
            (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') .
            $_SERVER['HTTP_HOST'] .
            (SETTINGS['hosted-locally'] ? '/IsiFitGems/' : '/')
        ?>" target="_self" />
        <link rel="stylesheet" type="text/css" href="style/pico.classless.min.css" />
        <link rel="stylesheet" type="text/css" href="style/style.css" />
        <!-- TODO decide whether to do this
        <link rel="sitemap" type="application/xml" title="Sitemap" href="sitemap.xml" />
        -->
        <link rel="icon" type="image/x-icon" href="icon.ico" />
<?php foreach ($page->scripts as $script): ?>
        <script src="<?= $script ?>" type="text/javascript"></script>
<?php endforeach ?>
<?php foreach ($prefetch as $resource): ?>
        <link rel="prefetch" href="<?= $resource ?>" />
<?php endforeach ?>
        <title><?= $page->title ?></title>
    </head>
    <body>
<?php 
if ($page->has_navbar)
    require "components/navbar.php";
?>
    <button id="theme_switcher"></button>
<?php
echo($body);
if ($page->has_feet)
    require "components/footer.php";
?>
    </body>
</html>
