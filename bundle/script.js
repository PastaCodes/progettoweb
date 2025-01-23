import { formatPrice } from '../scripts/util.js';
import { addBundleToCart } from '../scripts/cart.js';

// Wait for the style to be rendered
window.addEventListener('load', () => {
    // Retrieve the elements to be used when no thumbnail is available
    const noThumbnailTemplate = document.getElementById('no-thumbnail');
    // Cart stuff
    const addToCartBtn = document.querySelector('main > section > button');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', () => addBundleToCart(
            addToCartBtn.getAttribute('data-bundle-name'),
            addToCartBtn.getAttribute('data-variant-suffix')
        ));
    }
    const products = document.querySelectorAll('main > section > section:first-of-type > div');
    const variantDisplay = document.querySelector('main > section > section:nth-of-type(2) > p');
    const priceDisplay = document.querySelector('main > section > p');
    const radiosSection = document.querySelector('main > section > section:nth-of-type(2) > div');
    if (radiosSection) {
        const radios = Array.from(radiosSection.children);
        // When hovering over a radio button, the associated thumbnail is displayed
        // Otherwise the one associated with the checked button is displayed
        radios.forEach(radio => {
            const updateVariant = (radio) => {
                variantDisplay.innerHTML = radio.getAttribute('title');
                products.forEach(product => {
                    const variantsData = JSON.parse(product.getAttribute('data-variants-data'));
                    const variantData = variantsData[radio.getAttribute('data-variant-suffix')];
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
                    const productVariantDisplay = product.querySelector('p:nth-last-child(2)');
                    if (productVariantDisplay !== null) {
                        productVariantDisplay.innerHTML = variantDisplay.innerHTML;
                    }
                });
                priceDisplay.innerHTML = '<del>' + formatPrice(parseFloat(radio.getAttribute('data-price-before'))) +
                    '</del> <ins>' + formatPrice(parseFloat(radio.getAttribute('data-price'))) + '</ins>';
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
