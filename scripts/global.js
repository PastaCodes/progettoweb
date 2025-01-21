import { createCookie, getCookie, deleteCookie } from "./cookie.js";

function timeAgo(ms) {
    const seconds = Math.floor(ms / 1000);
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);
    if (seconds < 60) {
        return `less than a minute ago`;
    } else if (minutes < 60) {
        return `${minutes} minute${minutes !== 1 ? 's' : ''} ago`;
    } else if (hours < 24) {
        return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
    } else {
        return `${days} day${days !== 1 ? 's' : ''} ago`;
    }
}

const NOTIFICATIONS_LOCAL_STORAGE = 'notifications';
const ACCESSIBILITY_COOKIE_NAME = 'accessibility';

document.addEventListener('DOMContentLoaded', () => {
    // ===== Colortheme switch =====
    // Get document tag
    const docElt = document.documentElement;
    const btn = document.querySelector('#side-buttons > :first-child');
    const use = btn.querySelector('svg > use');
    // Function to switch the theme of the page
    const switchTheme = (themeStr) => {
        // Switch button icon
        use.setAttribute('href', themeStr === 'dark' ? 'assets/darkmode.svg#darkmode' : 'assets/lightmode.svg#lightmode');
        const btnTitle = 'Switch to ' + (themeStr === 'dark' ? 'light' : 'dark') + ' theme';
        btn.setAttribute('title', btnTitle);
        // Switch page color theme
        docElt.setAttribute('data-theme', themeStr);
        // Set theme preference to new theme
        localStorage.setItem('theme', themeStr);
    };
    switchTheme(localStorage.getItem('theme') || ((window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light'));
    // Switch theme on button press
    btn.addEventListener('click', () => {
        switchTheme(docElt.getAttribute('data-theme') === 'light' ? 'dark' : 'light');
    });
    // ===== Accessibility Options =====
    const updateCookie = () => {
        let accessibility = {};
        if (highContrastToggle.checked) {
            accessibility['high_contrast'] = true;
        }
        if (grayscaleToggle.checked) {
            accessibility['grayscale'] = true;
        }
        if (reducedStrainToggle.checked) {
            accessibility['reduced_strain'] = true;
        }
        if (largerTextToggle.checked) {
            accessibility['larger_text'] = true;
        }
        if (Object.keys(accessibility).length === 0) {
            deleteCookie(ACCESSIBILITY_COOKIE_NAME);
        } else {
            createCookie(ACCESSIBILITY_COOKIE_NAME, JSON.stringify(accessibility), 30 * 24 * 60 * 60 * 1000);
        }
    };
    const accessibilityButton = document.querySelector('#side-buttons > :last-child');
    accessibilityButton.addEventListener('click', () => accessibility.showModal());
    accessibility.querySelector('footer > button').addEventListener('click', () => accessibility.close());
    const highContrastToggle = accessibility.querySelector('[name="high-contrast"]');
    const grayscaleToggle = accessibility.querySelector('[name="grayscale"]');
    const reducedStrainToggle = accessibility.querySelector('[name="reduced-strain"]');
    const updateFilters = (doUpdateCookie = true) => {
        let filters = [];
        if (highContrastToggle.checked) {
            filters.push('contrast(120%)');
        }
        if (grayscaleToggle.checked) {
            filters.push('grayscale(100%)');
        }
        if (reducedStrainToggle.checked) {
            filters.push('contrast(90%) brightness(70%) sepia(30%) saturate(120%)');
        }
        accessibility.style.filter = docElt.style.filter = filters == [] ? 'none' : filters.join(' ');
        if (doUpdateCookie) {
            updateCookie();
        }
    };
    highContrastToggle.addEventListener('click', updateFilters);
    grayscaleToggle.addEventListener('click', updateFilters);
    reducedStrainToggle.addEventListener('click', updateFilters);
    updateFilters(false);
    const largerTextToggle = accessibility.querySelector('[name="larger-text"]');
    const factor = 1.1;
    const updateText = (doUpdateCookie = true) => {
        const adjust = largerTextToggle.checked ? factor : 1;
        docElt.style.setProperty('--font-size-adjust', adjust);
        if (doUpdateCookie) {
            updateCookie();
        }
    }
    largerTextToggle.addEventListener('click', updateText);
    updateText(false);
    // ===== Notification stuff =====
    // Update notification ticker function
    const updateNotificationTimestamps = () => {
        notifications.querySelectorAll('article > section').forEach(notification => {
            const currTimestamp = new Date(notification.getAttribute('data-timestamp'));
            const timeTicker = notification.querySelector("p:first-of-type");
            const differenceMillis = Date.now() - currTimestamp;
            timeTicker.innerHTML = timeAgo(differenceMillis);
        });
    };
    // Add callbacks to the link
    const notificationLink = document.querySelector('nav > ul :nth-child(4) > a');
    let notifInterval = null;
    notificationLink.addEventListener('click', () => {
        // Start updating notification counter
        updateNotificationTimestamps();
        if (!notifInterval) {
            notifInterval = setInterval(() => {
                updateNotificationTimestamps();
            }, 60000);
        }
        // Show notification modal
        notifications.showModal();
    });
    // Exit from notification modal properly
    const closeModal = () => {
         notifications.close();
        if (notifInterval) {
            clearInterval(notifInterval);
            notifInterval = null;
        }
    };
    notifications.querySelector("footer > button").addEventListener('click', () => {
        closeModal(); 
    });
    notifications.addEventListener('keydown', (e) => {
        if (e.key === "Escape") {
            closeModal();
        }
    });
    // Function to edit section visibility based on it being read
    const updateSectionRead = (section) => {
        const readNotifications = JSON.parse(localStorage.getItem(NOTIFICATIONS_LOCAL_STORAGE));
        const notificationId = section.getAttribute('data-id');
        if (readNotifications && readNotifications.includes(notificationId)) {
            section.style.filter = 'brightness(0.5)';
            section.style.display = checkboxHideRead.checked ? 'none' : '';
        } else {
            section.style.filter = '';
            section.style.display = '';
        }
    };
    const checkboxHideRead = notifications.querySelector('header > label > input');
    checkboxHideRead.addEventListener('click', () => {
        notifications.querySelectorAll('section').forEach(e => updateSectionRead(e));
    });
    // Setup read button
    notifications.querySelectorAll('article > section > button:first-of-type').forEach(btn => {
        updateSectionRead(btn.parentElement);
        btn.addEventListener('click', () => {
            const readNotifications = JSON.parse(localStorage.getItem(NOTIFICATIONS_LOCAL_STORAGE));
            const notificationId = btn.parentElement.getAttribute('data-id');
            // If there is no notification, add a new local storage item with the id
            if (!readNotifications) {
                localStorage.setItem(NOTIFICATIONS_LOCAL_STORAGE, JSON.stringify([notificationId]));
            } else {
            // If it was not read, add it to the list
                if (!readNotifications.includes(notificationId)) {
                    readNotifications.push(notificationId);
                    localStorage.setItem(NOTIFICATIONS_LOCAL_STORAGE, JSON.stringify(readNotifications));
                } else {
                    // Remove the element from the list if it was read
                    const idx = readNotifications.indexOf(notificationId);
                    readNotifications.splice(idx, 1);
                    localStorage.setItem(NOTIFICATIONS_LOCAL_STORAGE, JSON.stringify(readNotifications));
                    if (readNotifications.length <= 0) {
                        localStorage.removeItem(NOTIFICATIONS_LOCAL_STORAGE);
                    }
                }
            }
            updateSectionRead(btn.parentElement);
        });
    });
    // Setup erase button
    notifications.querySelectorAll('article > section > button:last-of-type').forEach(btn => {
        btn.addEventListener('click', () => {
            const notificationId = btn.parentElement.getAttribute('data-id');
            console.log("ERASE", notificationId);
        });
    });
});
