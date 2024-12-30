// Wait for the style to be rendered
window.addEventListener('load', () => {
    // Retrieve the elements to be used when no thumbnail is available
    const noThumbnailTemplate = document.getElementById('no-thumbnail');
    document.querySelectorAll('article').forEach(article => {
        const radiosSection = article.querySelector('section:nth-child(2):not(:last-child)');
        if (!radiosSection)
            return; // Skip to next article
        const thumbnailSection = article.querySelector('section:nth-child(1)');
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
        const children = Array.from(radiosSection.children);
        // When hovering over a radio button, the associated thumbnail is displayed
        // Otherwise the one associated with the checked button is displayed
        children.forEach(radio => {
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
        // Up to 5 radio buttons can be displayed neatly without needing the scrolling functionality
        if (children.length > 5) {
            // When clicking on a section assign a moveHandler and an upHandler (if it doesn't have one already)
            radiosSection.addEventListener('pointerdown', ev => {
                if (!radiosSection.moveHandler) {
                    radiosSection.moveHandler = ev => {
                        if (ev.pointerId === radiosSection.moveHandler.id) {
                            radiosSection.scrollBy(radiosSection.moveHandler.lastX - ev.clientX, 0);
                            radiosSection.moveHandler.lastX = ev.clientX;
                            // Any movement of more than 10 px is considered intentional,
                            // which means no click action should occur
                            if (Math.abs(radiosSection.moveHandler.lastX - radiosSection.moveHandler.initialX) > 10)
                                radiosSection.moveHandler.target = null;
                        }
                    };
                    radiosSection.moveHandler.id = ev.pointerId;
                    radiosSection.moveHandler.initialX = ev.clientX;
                    radiosSection.moveHandler.lastX = ev.clientX;
                    radiosSection.moveHandler.target = ev.target;
                    window.addEventListener('pointermove', radiosSection.moveHandler);
                    let upHandler = ev => {
                        if (radiosSection.moveHandler && radiosSection.moveHandler.id === ev.pointerId) {
                            if (radiosSection.moveHandler.target && radiosSection.moveHandler.target.type === 'radio') {
                                let clickEvent = new PointerEvent('click');
                                clickEvent.isProgrammatic = true;
                                radiosSection.moveHandler.target.dispatchEvent(clickEvent);
                            }
                            window.removeEventListener('pointermove', radiosSection.moveHandler);
                            radiosSection.moveHandler = null;
                            window.removeEventListener('pointerup', upHandler);
                        }
                    }
                    window.addEventListener('pointerup', upHandler);
                }
            });
            // Scrolls to have the selected radio button in the center (or close to it)
            const updateScroll = () => {
                const radio = children[radiosSection.selectedIndex];
                const radioStyle = window.getComputedStyle(radio);
                const radiosSectionStyle = window.getComputedStyle(radiosSection);
                // To avoid having too many radio buttons hidden, and also remain consistent with the '5 policy',
                // clamp the index so that, when clicking the first and second buttons, the third gets centered,
                // and, similarly, when clicking the last and second-to-last buttons, the third-to-last gets centered
                const clampedIndex = Math.min(Math.max(radiosSection.selectedIndex, 2), children.length - 3);
                const offset = (clampedIndex - 2) * (parseFloat(radioStyle.width) + parseFloat(radiosSectionStyle.gap));
                radiosSection.scrollTo({
                    top: 0,
                    left: offset,
                    behavior: 'smooth'
                });
            };
            radiosSection.selectedIndex = 0;
            updateScroll();
            children.forEach((radio, index) => {
                radio.addEventListener('click', ev => {
                    if (ev.isProgrammatic) {
                        radiosSection.selectedIndex = index;
                        updateScroll();
                    } else {
                        // Ignore/disable all events triggered directly by the browser
                        ev.preventDefault();
                        ev.stopPropagation();
                    }
                });
            });
            // The scroll position might need to be updated when zooming or resizing
            // This is likely because of the font size breakpoints set by pico
            window.addEventListener('resize', updateScroll);
        }
    });
});
