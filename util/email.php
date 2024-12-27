<?php
require __DIR__ . '/../classes/Script.php';

function send_email(string $recipient, string $subject, string $email_body) {
    if (SETTINGS['hosted-locally']) {
        ob_start();
        require __DIR__ . '/../components/email_base.php';
        $contents = ob_get_clean();
        ob_start();
        require __DIR__ . '/../components/email_container.php';
        $container = ob_get_clean();
        global $scripts;
        $scripts[] = Script::customInternal('email-contents', 'application/json', json_encode($container));
        $scripts[] = Script::jsExternal('scripts/email.js');
    } else {
        // Not implemented
    }
}
?>