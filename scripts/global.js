import { createCookie, deleteCookie } from "./cookie.js";

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
    const notifications = document.querySelector('#notifications');
    if (notifications !== null) {
        // Constants and Selectors
        const notificationLink = document.querySelector('#side-buttons > button:nth-child(2)');
        const notificationCounterElt = notificationLink.querySelector('span');
        const checkboxHideRead = notifications.querySelector('header > label > input');
        const closeButton = notifications.querySelector('footer > button');
        // Helper Functions
        const getReadNotifications = () => JSON.parse(localStorage.getItem(NOTIFICATIONS_LOCAL_STORAGE)) || [];
        const setReadNotifications = (notifications) => {
            if (notifications.length > 0) {
                localStorage.setItem(NOTIFICATIONS_LOCAL_STORAGE, JSON.stringify(notifications));
            } else {
                localStorage.removeItem(NOTIFICATIONS_LOCAL_STORAGE);
            }
        };
        const updateSectionRead = (section, readBtn) => {
            const readNotifications = getReadNotifications();
            const notificationId = section.getAttribute('data-id');
            const isRead = readNotifications.includes(notificationId);
            readBtn.innerHTML = isRead ? 'U' : 'R';
            section.style.filter = isRead ? 'brightness(0.5)' : '';
            section.style.display = isRead && checkboxHideRead.checked ? '' : 'initial';
        };
        const toggleReadStatus = (notificationId, isRead) => {
            let readNotifications = getReadNotifications();
            if (isRead) {
                readNotifications = readNotifications.filter(id => id !== notificationId);
            } else {
                readNotifications.push(notificationId);
            }
            setReadNotifications(readNotifications);
            updateUnreadCounter();
        };
        const updateUnreadCounter = () => {
            const totalNotifications = notifications.querySelectorAll('article > section').length;
            const readNotifications = getReadNotifications().length;
            const unreadCount = totalNotifications - readNotifications;
            notificationCounterElt.innerHTML = unreadCount > 0 ? unreadCount : '';
            if (unreadCount > 0) {
                notificationCounterElt.style.display = '';
                notificationCounterElt.style.animation = '';
            } else {
                notificationCounterElt.style.display = 'none';
                notificationCounterElt.style.animation = 'none';
                void notificationCounterElt.offsetWidth;
            }
        };
        const updateNotificationTimestamps = () => {
            notifications.querySelectorAll('article > section').forEach(notification => {
                const currTimestamp = new Date(notification.getAttribute('data-timestamp'));
                const timeTicker = notification.querySelector("p:first-of-type");
                const differenceMillis = Date.now() - currTimestamp;
                timeTicker.innerHTML = timeAgo(differenceMillis);
            });
        };
        let notifInterval = null;
        const closeModal = () => {
            notifications.close();
            if (notifInterval) {
                clearInterval(notifInterval);
                notifInterval = null;
            }
        };
        // Event Listeners
        notificationLink.addEventListener('click', ev => {
            ev.preventDefault();
            updateNotificationTimestamps();
            updateUnreadCounter();
            if (!notifInterval) {
                notifInterval = setInterval(updateNotificationTimestamps, 60000);
            }
            notifications.showModal();
            return false;
        });
        closeButton.addEventListener('click', closeModal);
        notifications.addEventListener('keydown', (e) => {
            if (e.key === "Escape") {
                closeModal();
            }
        });
        checkboxHideRead.addEventListener('click', () => {
            notifications.querySelectorAll('section').forEach(s => updateSectionRead(s, s.querySelector('button:first-of-type')));
        });
        // Initialize Read Button Listeners
        notifications.querySelectorAll('article > section > button:first-of-type').forEach(btn => {
            const section = btn.parentElement;
            updateSectionRead(section, btn);
            btn.addEventListener('click', () => {
                const notificationId = section.getAttribute('data-id');
                const isRead = getReadNotifications().includes(notificationId);
                toggleReadStatus(notificationId, isRead);
                updateSectionRead(section, btn);
            });
        });
        // Setup erase button
        notifications.querySelectorAll('article > section > button:last-of-type').forEach(btn => {
            btn.addEventListener('click', () => {
                const notificationId = btn.parentElement.getAttribute('data-id');
                console.log("ERASE", notificationId);
            });
        });
        updateUnreadCounter();
    }
});
