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
        <section>
            <h1><?= $subject ?></h1>
            <p>From: noreply@isifitgems.it</p>
            <p>To: mario.rossi@gmail.com</p>
            <iframe src="data:text/html;base64,<?= base64_encode($contents) ?>" width="700" height="1000"></iframe>
        </section>
    </body>
</html>
