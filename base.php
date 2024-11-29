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
        <?php if ($page->settings['allow-indexing']): ?>
        <meta name="robots" content="index, follow" />
        <?php else: ?>
        <meta name="robots" content="none" />
        <?php endif ?>
        <?php if (SETTINGS['hosted-locally']): ?>
        <base href="<?= (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] ?>/IsiFitGems/" target="_self" />
        <?php else: ?>
        <base href="https://isifitgems.it/" target="_self" />
        <?php endif ?>
        <link rel="stylesheet" type="text/css" href="pico.classless.min.css" />
        <!-- TODO decide whether to do this
        <link rel="sitemap" type="application/xml" title="Sitemap" href="sitemap.xml" />
        -->
        <link rel="icon" type="image/x-icon" href="icon.ico" />
        <title><?= $page->title ?></title>
    </head>
    <body>
    <?php require $page->body ?>
    </body>
</html>