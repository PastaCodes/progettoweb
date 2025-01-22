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
}
// Redirect if username in session
if (isset($_SESSION['username'])) {
    header('Location: ../shop');
    exit();
}

?>
    <main>
        <form method="POST">
            <fieldset>
                <label>
                    Username <small><?= $register_error ? 'An account with that username already exists!' : ''?></small>
                    <input minlength="1" maxlength="255" type="text" name="username" placeholder="Username" required="required">
                </label>
                <label>
                    Password <small></small>
                    <input minlength="8" maxlength="255" type="password" name="password" placeholder="Password" required="required">
                </label>
                <input type="submit" value="Register">
            </fieldset>
        </form>
    </main>
