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
    // Login to account
    $_SESSION['username'] = $username;
}
// Redirect if username in session
if (isset($_SESSION['username'])) {
    header('Location: ../shop');
    exit();
}
?>
    <main>
        <form method="POST">
            <h2>Login</h2>
            <fieldset>
                <label>
                    Username <small><?= $login_error ? 'Invalid login details!' : ''?></small>
                    <input minlength="1" maxlength="255" type="text" name="username" autocomplete="username" placeholder="Username" required="required">
                </label>
                <label>
                    Password <small></small>
                    <input minlength="8" maxlength="255" type="password" name="password" autocomplete="current-password" placeholder="Password" required="required">
                </label>
                <p><a href="register">I don't have an account</a> &ndash; <a>I forgot my password</a></p>
                <input type="submit" value="Login">
            </fieldset>
        </form>
    </main>
