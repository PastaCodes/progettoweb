// Wait for the style to be rendered
window.addEventListener('load', () => {
    // Cart stuff
    const addToCartBtn = document.querySelector('main > button');
    if (addToCartBtn) {
        let cartBtnHandler = null;
        const setCartButtonEvent = (productId, variantId) => {
            addToCartBtn.removeEventListener('click', cartBtnHandler);
            cartBtnHandler = () => modifyCart(productId, variantId);
            addToCartBtn.addEventListener('click', cartBtnHandler);
        };
        const setCartButtonEventRadio = (radio) => {
            const variantId = radio.getAttribute('data-variant-suffix');
            const url = new URL(window.location.href);
            const productId = new URLSearchParams(url.search).get('id');
            setCartButtonEvent(productId, variantId);
        };
        document.querySelectorAll('[type="radio"]').forEach(radio => {
            radio.addEventListener('click', () => setCartButtonEventRadio(radio));
            if (radio.checked) {
                setCartButtonEventRadio(radio);
            }
        });
        // Set cart button base event
        setCartButtonEvent(new URLSearchParams(new URL(window.location.href).search).get('id'));
    }
    // Retrieve the elements to be used when no thumbnail is available
    const noThumbnailTemplate = document.getElementById('no-thumbnail');
    const radiosSection = document.querySelector('main > div:nth-child(2)');
    if (radiosSection) {
        const thumbnailElement = document.querySelector('main > :not(h1):first-child');
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
            });
            radio.addEventListener('mouseover', () => {
                if (!radio.checked) {
                    displayThumbnail(radio);
                }
            });
            radio.addEventListener('mouseout', () => {
                if (!radio.checked) {
                    displayThumbnail(radiosSection.querySelector(':checked'));
                }
            });
            const transition = radio.style.transition;
            radio.style.transition = 'none'; // Disable transition momentarily
            // For silly little browsers that do not support attr styling
            radio.style.setProperty('--radio-color', radio.getAttribute('data-color'));
            requestAnimationFrame(() => {
                radio.style.transition = transition;
            });
        });
    }
});
