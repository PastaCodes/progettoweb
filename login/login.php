<?php
require_once '../classes/Account.php';

$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;
$login_error = false;
if ($username) {
    $login_error = true;
}
if ($username && $password && Account::check_login($username, $password)) {
    $login_error = false;
    // TODO: Log into the account, start the session and redirect to store?
}
?>
    <main>
        <form method="POST">
            <fieldset>
                <label>
                    Username <small><?= $login_error ? 'Invalid login details!' : ''?></small>
                    <input minlength="1" maxlength="255" type="text" name="username" placeholder="Username" required="required">
                </label>
                <label>
                    Password <small></small>
                    <input minlength="8" maxlength="255" type="password" name="password" placeholder="Password" required="required">
                </label>
                <input type="submit" value="Login">
            </fieldset>
        </form>
    </main>
