import { createCookie, getCookie, deleteCookie } from "./cookie.js";

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
            deleteCookie('accessibility');
        } else {
            createCookie('accessibility', JSON.stringify(accessibility), 30 * 24 * 60 * 60 * 1000);
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
    const notificationLink = document.querySelector('nav > ul :nth-child(4) > a');
    notificationLink.addEventListener('click', () => {
        notifications.showModal();
    });
    notifications.querySelector("footer > button").addEventListener('click', () => {
        notifications.close();
    });
});
