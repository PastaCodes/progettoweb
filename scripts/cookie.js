// The time is calculated in ms (so to make it last a full day it must be `24 * 60 * 60 * 1000`)
export function createCookie(name, value, time) {
    const date = new Date();
    date.setTime(date.getTime() + time);
    const expires = `expires = ${date.toUTCString()}; `;
    document.cookie = `${name} = ${value || ""}; ${expires || ""}path=/`;
}

export function deleteCookie(name) {
    document.cookie = `${name} =; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; Max-Age=-99999999;`;
}

export function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') 
            c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) 
            return c.substring(nameEQ.length, c.length);
    }
    return null;
}
