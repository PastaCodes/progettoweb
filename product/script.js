// Wait for the style to be rendered
window.addEventListener('load', () => {
    // Retrieve the elements to be used when no thumbnail is available
    const noThumbnailTemplate = document.getElementById('no-thumbnail');
    const radiosSection = document.querySelector('main > section:nth-child(2):not(:nth-last-child(2))');
    if (radiosSection) {
        const thumbnailSection = document.querySelector('main > section:nth-child(1)');
        const displayThumbnail = (activeRadio) => {
            let img = thumbnailSection.querySelector('img');
            const thumbnail = activeRadio.getAttribute('data-thumbnail');
            if (thumbnail && !img) {
                // Replace the 'no thumbnail' elements with a new img
                thumbnailSection.replaceChildren(img = document.createElement('img'));
            }
            if (thumbnail) {
                // Reuse the already present img to avoid flashes
                img.src = thumbnail;
                img.loading = 'eager';
            } else if (img) {
                // Replace the img with the 'no thumbnail' elements
                thumbnailSection.innerHTML = noThumbnailTemplate.innerHTML;
            }
        }
        const radios = Array.from(radiosSection.children);
        // When hovering over a radio button, the associated thumbnail is displayed
        // Otherwise the one associated with the checked button is displayed
        radios.forEach(radio => {
            radio.addEventListener('click', () => {
                let variant = radio.getAttribute('data-variant-suffix');
                let url = new URL(window.location.href);
                url.searchParams.set('variant', variant);
                window.history.replaceState(null, '', url.toString());
            });
            radio.addEventListener('mouseover', () => {
                if (!radio.checked)
                    displayThumbnail(radio);
            });
            radio.addEventListener('mouseout', () => {
                if (!radio.checked)
                    displayThumbnail(radiosSection.querySelector(':checked'));
            });
            // For silly little browsers that do not support attr styling
            radio.style.setProperty('--radio-color', radio.getAttribute('data-color'));
        });
    }
});
