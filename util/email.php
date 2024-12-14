<?php
require_once 'classes/Script.php';
function send_email(Page $email_page) {
    if (SETTINGS['hosted-locally']) {
        ob_start();
        require 'components/email_base.php';
        $contents = ob_get_clean();
        global $scripts;
        $scripts[] = Script::customInternal('email-contents', 'application/json', json_encode($contents));
        $scripts[] = Script::jsExternal('email.js');
    } else {
        // Not implemented
    }
}
?>