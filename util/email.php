<?php
function send_email(string $recipient, string $subject, string $email_body) {
    if (SETTINGS['hosted-locally']) {
        ob_start();
        require_once __DIR__ . '/../components/email_base.php';
        $contents = ob_get_clean();
        ob_start();
        require_once __DIR__ . '/../components/email_container.php';
        $container = ob_get_clean();
        global $page;
        $page->scripts[] = Script::internal('let emailContents = \'' . base64_encode($container) . '\';');
        $page->scripts[] = Script::external('scripts/email.js');
    } else {
        // Not implemented
    }
}
?>
