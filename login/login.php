<?php
require_once '../classes/Account.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ../login');
    exit();
}
// Handle login
$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;
$login_error = false;
if ($username && $password) {
    $login_error = true;
    $is_vendor = Account::check_login($username, $password);
    if ($is_vendor !== null) {
        $login_error = false;
        // Login to account
        $_SESSION['username'] = $username;
        $_SESSION['vendor'] = $is_vendor;
    }
}
// Redirect if username in session
if (isset($_SESSION['username'])) {
    $redirect_page = $_COOKIE['last_page'] ?? '../shop';
    header("Location: $redirect_page");
    exit();
}
?>
    <main>
        <form method="POST">
            <h2>Login</h2>
            <fieldset>
                <label for="username">
                    Username
                    <input id="username" minlength="1" maxlength="255" type="text" name="username" autocomplete="username" placeholder="Username" required="required">
                </label>
                <label for="password">
                    Password
                    <input id="password" minlength="1" maxlength="255" type="password" name="password" autocomplete="current-password" placeholder="Password" required="required">
<?php if ($login_error): ?>
                    <small>Invalid login details!</small>
<?php endif ?>
                </label>
                <p><a href="register">I don't have an account</a> &ndash; <a>I forgot my password</a></p>
                <input type="submit" value="Login">
            </fieldset>
        </form>
    </main>
