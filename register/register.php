<?php
// TODO: Handle register
?>
    <main>
        <form method="POST">
            <fieldset>
                <label>
                    Username <small></small>
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
