import { formatPrice } from '../scripts/util.js';
import { addProductToCart } from '../scripts/cart.js';

// Wait for the style to be rendered
window.addEventListener('load', () => {
    // Retrieve the elements to be used when no thumbnail is available
    const noThumbnailTemplate = document.getElementById('no-thumbnail');
    // Cart stuff
    const addToCartBtn = document.querySelector('main > section > button');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', () => addProductToCart(
            addToCartBtn.getAttribute('data-product-name'),
            addToCartBtn.getAttribute('data-variant-suffix')
        ));
    }
    const variantDisplay = document.querySelector('main > section > p:nth-last-of-type(2)');
    const priceDisplay = document.querySelector('main > section > p:last-of-type');
    const radiosSection = document.querySelector('main > section > div:nth-child(2)');
    if (radiosSection) {
        let thumbnailElement = document.querySelector('main > section > :first-child');
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
        const radios = Array.from(radiosSection.children);
        // When hovering over a radio button, the associated thumbnail is displayed
        // Otherwise the one associated with the checked button is displayed
        radios.forEach(radio => {
            radio.addEventListener('click', () => {
                const variant = radio.getAttribute('data-variant-suffix');
                const url = new URL(window.location.href);
                url.searchParams.set('variant', variant);
                window.history.replaceState(null, '', url.toString());
                addToCartBtn?.setAttribute('data-variant-suffix', variant);
                displayThumbnail(radio);
                variantDisplay.innerHTML = radio.getAttribute('title');
                priceDisplay.innerHTML = formatPrice(parseFloat(radio.getAttribute('data-price')));
            });
            radio.addEventListener('mouseover', () => {
                if (!radio.checked) {
                    displayThumbnail(radio);
                    variantDisplay.innerHTML = radio.getAttribute('title');
                    priceDisplay.innerHTML = formatPrice(parseFloat(radio.getAttribute('data-price')));
                }
            });
            radio.addEventListener('mouseout', () => {
                if (!radio.checked) {
                    const checked = radiosSection.querySelector(':checked');
                    displayThumbnail(checked);
                    variantDisplay.innerHTML = checked.getAttribute('title');
                    priceDisplay.innerHTML = formatPrice(parseFloat(checked.getAttribute('data-price')));
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
