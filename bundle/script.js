// import { formatPrice } from '../scripts/global.js';

// Wait for the style to be rendered
window.addEventListener('load', () => {
    // Retrieve the elements to be used when no thumbnail is available
    const noThumbnailTemplate = document.getElementById('no-thumbnail');
    // Cart stuff
    const addToCartBtn = document.querySelector('main > button');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', () => { }); // TODO
    }
    const products = document.querySelectorAll('main > section:first-of-type > div');
    const variantDisplay = document.querySelector('main > section:nth-of-type(2) > p');
    const priceDisplay = document.querySelector('main > p');
    const radiosSection = document.querySelector('main > section:nth-of-type(2) > div');
    if (radiosSection) {
        /*
        let thumbnailElement = document.querySelector('main > :not(h1):first-child');
        const displayThumbnail = (activeRadio) => {
            const isImage = thumbnailElement instanceof HTMLImageElement;
            const thumbnailFile = activeRadio.getAttribute('data-thumbnail-file');
            const thumbnailAltText = activeRadio.getAttribute('data-thumbnail-alt');
            if (thumbnailFile && !isImage) {
                // Replace the 'no thumbnail' elements with a new img
                const img = document.createElement('img');
                thumbnailElement.replaceWith(img);
                thumbnailElement = img;
            }
            if (thumbnailFile) {
                // Reuse the already present img to avoid flashes
                thumbnailElement.src = thumbnailFile;
                thumbnailElement.alt = thumbnailAltText;
                thumbnailElement.loading = 'eager';
            } else if (isImage) {
                // Replace the img with the 'no thumbnail' elements
                const noThumbnail = noThumbnailTemplate.content.cloneNode(true).firstElementChild;
                thumbnailElement.replaceWith(noThumbnail);
                thumbnailElement = noThumbnail;
            }
        }
            */
        const radios = Array.from(radiosSection.children);
        // When hovering over a radio button, the associated thumbnail is displayed
        // Otherwise the one associated with the checked button is displayed
        radios.forEach(radio => {
            const updateVariant = (radio) => {
                variantDisplay.innerHTML = radio.getAttribute('title');
                // TODO: thumbnails with alts
                let price = 0;
                products.forEach(product => {
                    const variantsData = JSON.parse(product.getAttribute('data-variants-data'));
                    const variantData = variantsData[radio.getAttribute('data-variant-suffix')];
                    price += variantData['price'];
                    let thumbnailElement = product.querySelector(':first-child');
                    const isImage = thumbnailElement instanceof HTMLImageElement;
                    const thumbnailFile = variantData['thumbnail_file'];
                    const thumbnailAltText = variantData['thumbnail_alt'];
                    if (thumbnailFile && !isImage) {
                        // Replace the 'no thumbnail' elements with a new img
                        const img = document.createElement('img');
                        thumbnailElement.replaceWith(img);
                        thumbnailElement = img;
                    }
                    if (thumbnailFile) {
                        // Reuse the already present img to avoid flashes
                        thumbnailElement.src = thumbnailFile;
                        thumbnailElement.alt = thumbnailAltText;
                        thumbnailElement.loading = 'eager';
                    } else if (isImage) {
                        // Replace the img with the 'no thumbnail' elements
                        const noThumbnail = noThumbnailTemplate.content.cloneNode(true).firstElementChild;
                        thumbnailElement.replaceWith(noThumbnail);
                        thumbnailElement = noThumbnail;
                    }
                });
                const multiplier = parseFloat(priceDisplay.getAttribute('data-multiplier'));
                priceDisplay.innerHTML = '<del>' + formatPrice(price) + '</del> ' + formatPrice(multiplier * price);
            }
            radio.addEventListener('click', () => {
                const variant = radio.getAttribute('data-variant-suffix');
                const url = new URL(window.location.href);
                url.searchParams.set('variant', variant);
                window.history.replaceState(null, '', url.toString());
                addToCartBtn?.setAttribute('data-variant-suffix', variant);
                updateVariant(radio);
            });
            radio.addEventListener('mouseover', () => {
                if (!radio.checked) {
                    updateVariant(radio);
                }
            });
            radio.addEventListener('mouseout', () => {
                if (!radio.checked) {
                    const checked = radiosSection.querySelector(':checked');
                    updateVariant(checked);
                }
            });
            // Set --radio-color manually because of silly little browsers that do not support attr styling
            // But first disable transition momentarily
            const transition = radio.style.transition;
            radio.style.transitionDuration = '0s';
            radio.style.setProperty('--radio-color', radio.getAttribute('data-color'));
            // Dear God
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    radio.style.transition = transition;
                });
            });
        });
    }
});
