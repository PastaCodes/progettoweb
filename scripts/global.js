import { createCookie, deleteCookie } from "./cookie.js";
import { deleteNotification } from "./notifications.js";

function timeAgo(ms) {
    const seconds = Math.floor(ms / 1000);
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);
    if (seconds < 60) {
        return `Moments ago`;
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
    const btn = document.querySelector('body > ul > li:first-child > button');
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
    switchTheme(docElt.getAttribute('data-theme') || ((window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light'));
    btn.firstElementChild.style.visibility = 'visible';
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
    const accessibility = document.querySelector('dialog:last-of-type')
    const accessibilityButton = document.querySelector('body > ul > li:last-child > button');
    accessibilityButton.addEventListener('click', () => accessibility.showModal());
    accessibility.querySelector('footer > button').addEventListener('click', () => accessibility.close());
    const highContrastToggle = accessibility.querySelector('[name="high-contrast"]');
    const grayscaleToggle = accessibility.querySelector('[name="grayscale"]');
    const reducedStrainToggle = accessibility.querySelector('[name="reduced-strain"]');
    const updateFilters = () => {
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
        updateCookie();
    };
    accessibility.style.filter = docElt.style.filter;
    highContrastToggle.addEventListener('click', updateFilters);
    grayscaleToggle.addEventListener('click', updateFilters);
    reducedStrainToggle.addEventListener('click', updateFilters);
    const largerTextToggle = accessibility.querySelector('[name="larger-text"]');
    largerTextToggle.addEventListener('click', () => {
        docElt.style.setProperty('--font-size-adjust', largerTextToggle.checked ? 1.3 : 1);
        updateCookie();
    });
    // ===== Notification stuff =====
    const notifications = document.querySelector('dialog:nth-last-of-type(2)');
    if (notifications) {
        // Constants and Selectors
        const notificationLink = document.querySelector('body > ul > li:nth-child(2) > button');
        const checkboxHideRead = notifications.querySelector('label > input');
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
            readBtn.querySelector('use').setAttribute('href', isRead ? 'assets/read.svg#read' : 'assets/unread.svg#unread');
            readBtn.title = isRead ? 'Mark as unread' : 'Mark as read';
            section.style.filter = isRead ? 'brightness(0.5)' : '';
            section.style.display = isRead && checkboxHideRead.checked ? 'none' : '';
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
            const totalNotifications = notifications.querySelectorAll('article > ul > li').length;
            const readNotifications = getReadNotifications().length;
            const unreadCount = totalNotifications - readNotifications;
            notificationLink.style.setProperty('--counter-content', unreadCount > 0 ? `"${unreadCount}"` : '""');
            if (unreadCount > 0) {
                notificationLink.style.setProperty('--counter-visibility', 'visible');
                notificationLink.style.setProperty('--counter-animation', '');
            } else {
                notificationLink.style.setProperty('--counter-visibility', 'hidden');
                notificationLink.style.setProperty('--counter-animation', 'none');
            }
        };
        const updateNotificationTimestamps = () => {
            notifications.querySelectorAll('article > ul > li').forEach(notification => {
                const currTimestamp = new Date(notification.getAttribute('data-timestamp'));
                const timeTicker = notification.querySelector('p:last-of-type');
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
        notifications.addEventListener('keydown', ev => {
            if (ev.key === 'Escape') {
                closeModal();
            }
        });
        checkboxHideRead.addEventListener('click', () => {
            notifications.querySelectorAll('ul > li').forEach(s => updateSectionRead(s, s.querySelector('button:first-of-type')));
        });
        // Initialize Read Button Listeners
        notifications.querySelectorAll('article > ul > li > button:first-of-type').forEach(btn => {
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
        notifications.querySelectorAll('article > ul > li > button:last-of-type').forEach(btn => {
            btn.addEventListener('click', () => {
                const notificationId = btn.parentElement.getAttribute('data-id');
                deleteNotification(notificationId); 
                btn.parentElement.remove();
                setReadNotifications(getReadNotifications().filter(v => v != notificationId));
                updateUnreadCounter();
            });
        });
        updateUnreadCounter();
    }
    const hamburger = document.querySelector('header > button');
    const navbar = document.querySelector('header > nav');
    hamburger.addEventListener('click', () => {
        if (navbar.style.transform) {
            navbar.style.transform = '';
            hamburger.querySelector('use').setAttribute('href', 'assets/hamburger.svg#hamburger');
            hamburger.ariaLabel = 'Open menu';
            const animHandle = () => {
                navbar.style.visibility = '';
                navbar.removeEventListener('transitionend', animHandle);
            }
            navbar.addEventListener('transitionend', animHandle);
        } else {
            navbar.style.visibility = 'visible';
            navbar.style.transform = 'translateX(0)';
            hamburger.querySelector('use').setAttribute('href', 'assets/close.svg#close');
            hamburger.ariaLabel = 'Close menu';
        }
    });
});
