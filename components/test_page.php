<?php
require 'util/email.php';
send_email(new Page('Confirm your order', 'components/test_email.php'));
?>
        <main>
            <span>We've sent you a confirmation email.</span>
        </main>
