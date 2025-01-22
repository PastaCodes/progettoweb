import { setupForm } from '../scripts/form.js'

document.addEventListener('DOMContentLoaded', () => {
    const formElement = document.querySelector('form');
    const usernameInput = formElement.querySelector('fieldset > label:first-of-type > input');
    const passwordInput = formElement.querySelector('fieldset > label:last-of-type > input');
    setupForm(formElement, usernameInput, passwordInput);
});
