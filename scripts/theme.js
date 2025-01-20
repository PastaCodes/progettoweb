document.addEventListener('DOMContentLoaded', () => {
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
});
