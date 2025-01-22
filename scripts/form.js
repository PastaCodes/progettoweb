export function setupForm(formElement, usernameInput, passwordInput) {
    const checkErr = (errFun, input) => {
        const err = errFun(input);
        if (!err) {
            input.setAttribute('aria-invalid', false);
            input.parentElement.querySelector('small').innerHTML = '';
            return;
        }
        input.setAttribute('aria-invalid', true);
        input.parentElement.querySelector('small').innerHTML = err;
    };
    // Check errors on change
    usernameInput.addEventListener('input', ev => {
        checkErr(checkUser, ev.target);
    });
    passwordInput.addEventListener('input', ev => {
        checkErr(checkPassword, ev.target);
    });
    // Process data only if data is correct
    formElement.addEventListener('submit', ev => {
        // Prevent redirect to check data first
        ev.preventDefault();
        if (checkUser(usernameInput) || checkPassword(passwordInput)) {
            return;
        }
        // Redirect
        ev.target.submit();
    });
}

function checkUser(usernameElement) {
    const userPattern = /^[a-zA-Z0-9_]+$/;
    if (usernameElement.value.length < parseInt(usernameElement.minLength)) {
        return "Fill out this field.";
    }
    if (usernameElement.value.length > parseInt(usernameElement.maxLength)) {
        return "Username is too long.";
    }
    if (userPattern.test(usernameElement.value)) {
        return null;
    }
    return "Username can only contain letters, numbers, and underscores.";
}

function checkPassword(passwordElement) {
    const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%,._-^&+=!])[A-Za-z\d@#$%,._-^&+=!]+$/;
    if (passwordElement.value.length < parseInt(passwordElement.minLength)) {
        return "Password must be at least 8 characters long.";
    }
    if (passwordElement.value.length > parseInt(passwordElement.maxLength)) {
        return "Password is too long.";
    }
    if (passwordPattern.test(passwordElement.value)) {
        return null;
    }
    if (!/[a-z]/.test(passwordElement.value)) {
        return "Password must contain at least one lowercase letter.";
    }
    if (!/[A-Z]/.test(passwordElement.value)) {
        return "Password must contain at least one uppercase letter.";
    }
    if (!/\d/.test(passwordElement.value)) {
        return "Password must contain at least one number.";
    }
    if (!/[@#$%^&+=!]/.test(passwordElement.value)) {
        return "Password must contain at least one special character (@#$%,._-^&+=!).";
    }
    return "Password contains an invalid character.";
}
