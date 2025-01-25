import { addNotification } from "../scripts/notifications.js";

document.querySelectorAll('td > form > button').forEach(btn => {
    const form = btn.parentElement;
    const tableRow = form.parentElement.parentElement;
    const username = tableRow.querySelector('td:nth-of-type(2) > p').innerHTML;
    const rowStatus = tableRow.querySelector('td:nth-of-type(3) > select > option[selected]').innerHTML;
    form.addEventListener('submit', ev => {
        ev.preventDefault();
        addNotification('Update on your order!', `One of your order's status has updated to ${rowStatus}, check the orders page for more information.`, username);
        form.submit();
    });
});
