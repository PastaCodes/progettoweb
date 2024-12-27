document.addEventListener('DOMContentLoaded', () => {
    // Get document tag
    const docElt = document.documentElement;
    const btn = document.querySelector('#theme_switcher');
    // Function to switch the theme of the page
    const switchTheme = (themeStr) => {
        // Switch button text
        btn.innerHTML = themeStr === 'dark' ? '🌙' : '☀️';
        // Switch page color theme
        docElt.setAttribute('data-theme', themeStr);
        // Set theme preference to new theme
        localStorage.setItem('theme', themeStr);
    };
    switchTheme(localStorage.getItem('theme') || 'light');
    // Switch theme on button press
    btn.addEventListener('click', () => {
        switchTheme(docElt.getAttribute('data-theme') === 'light' ? 'dark' : 'light');
    });
});
