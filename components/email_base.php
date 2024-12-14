<?php
require 'util/files.php';
require 'util/format.php';
ob_start();
require $email_page->body;
$body = ob_get_clean();
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <base href="<?=
            (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') .
            $_SERVER['HTTP_HOST'] .
            (SETTINGS['hosted-locally'] ? '/IsiFitGems/' : '/')
        ?>" target="_blank" />
        <title><?= $email_page->title ?></title>
        <style type="text/css"><?= minify_css(read_file('style/pico.classless.min.css')) ?></style>
<?php if (SETTINGS['hosted-locally']): ?>
        <script type="text/javascript">
            window.addEventListener('unload', () => {
                if (window.opener)
                    window.opener.postMessage('revokeBlob');
            });
        </script>
<?php endif ?>
    </head>
    <body>
<?= $body ?>
    </body>
</html>
