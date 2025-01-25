<?php
require_once '../classes/Account.php';

$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;
$register_error = false;
if ($username) {
    $register_error = true;
}
if ($username && $password && !Account::check_exists($username)) {
    $register_error = false;
    // Create the account and add it to current session
    Account::add_to_db($username, $password); 
    $_SESSION['username'] = $username;
    $_SESSION['vendor'] = false; // Reigster is only for normal users
}
// Redirect if username in session
if (isset($_SESSION['username'])) {
    header('Location: ../shop');
    exit();
}
?>
    <main>
        <form method="POST">
            <h2>Register</h2>
            <fieldset>
                <label for="username">
                    Username
                    <input id="username" minlength="1" maxlength="255" type="text" name="username" autocomplete="username" placeholder="Username" required="required">
<?php if ($register_error): ?>
                    <small>An account with that username already exists!</small>
<?php endif ?>
                </label>
                <label for="password">
                    Password
                    <input id="password" minlength="8" maxlength="255" type="password" name="password" autocomplete="new-password" placeholder="Password" required="required">
                </label>
                <p><a href="login">I already have an account</a></p>
                <input type="submit" value="Register">
            </fieldset>
        </form>
    </main>
