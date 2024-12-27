<?php
ob_start();
require __DIR__ . '/../' . $page->body;
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
        <link rel="stylesheet" type="text/css" href="style/pico.classless.min.css">
        <link rel="stylesheet" type="text/css" href="style/theme_switcher.css" />
<?php foreach ($page->stylesheets as $stylesheet): ?>
        <link rel="stylesheet" type="text/css" href="<?= $stylesheet ?>" />
<?php endforeach ?>
        <link rel="stylesheet" type="text/css" href="style/style.css">
        <link rel="icon" type="image/x-icon" href="icon.ico">
        <script src="scripts/theme_switcher.js" type="text/javascript"></script>
<?php
foreach ($scripts as $script)
    echo $script->to_script_tag();
?>
<?php foreach ($prefetch as $resource): ?>
        <link rel="prefetch" href="<?= $resource ?>">
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