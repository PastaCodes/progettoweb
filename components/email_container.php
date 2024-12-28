<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <script type="text/javascript">
            window.addEventListener('beforeunload', () => {
                if (window.opener)
                    window.opener.postMessage('revokeBlob');
            });
        </script>
        <title>"<?= $subject ?>" Mock email</title>
    </head>
    <body>
        <h1><?= $subject ?></h1>
        <h2>From: noreply@isifitgems.com</h2>
        <h2>To: mario.rossi@gmail.com</h2>
        <iframe src="data:text/html;base64,<?= base64_encode($contents) ?>" width="700" height="1000"></iframe>
    </body>
</html>