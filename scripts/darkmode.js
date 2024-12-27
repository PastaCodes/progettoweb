document.addEventListener('DOMContentLoaded', () => {
    // Add dummy button
    // TODO: improve button code and position stuff 
    const btn = document.createElement("button");
    btn.innerHTML = "Change theme";
    document.querySelector("body").appendChild(btn);
    // Get document tag
    const docElt = document.documentElement;
    // Set current theme on the page
    const currentTheme = localStorage.getItem('theme') || 'light';
    docElt.setAttribute('data-theme', currentTheme);
    btn.addEventListener('click', () => {
        // Switch theme
        const newTheme = docElt.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
        docElt.setAttribute('data-theme', newTheme);
        // Set theme preference to new theme
        localStorage.setItem('theme', newTheme);
    });
});
