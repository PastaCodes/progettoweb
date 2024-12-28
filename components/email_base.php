<?php
require __DIR__ . '/../util/files.php';
require __DIR__ . '/../util/format.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <base href="<?=
            (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') .
            $_SERVER['HTTP_HOST'] .
            (SETTINGS['hosted-locally'] ? '/IsiFitGems/' : '/')
        ?>" target="_blank">
        <style type="text/css"><?= read_file('style/pico.classless.min.css') ?></style>
    </head>
    <body>
<?php
require __DIR__ . '/../' . $email_body;
?>
    </body>
</html>