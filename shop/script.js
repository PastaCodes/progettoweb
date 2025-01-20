// const radioTransitionRuleIndex = document.styleSheets[0].insertRule('[type="radio"] { transition: none; }');
// Wait for the style to be rendered
window.addEventListener('load', () => {
    // Retrieve the elements to be used when no thumbnail is available
    const noThumbnailTemplate = document.getElementById('no-thumbnail');
    document.querySelectorAll('main > div > div').forEach(article => {
        const radiosSection = article.querySelector('div:nth-child(2)');
        article.deadzonePointers = new Set();
        article.primingPointers = new Map();
        article.addEventListener('pointerdown', ev => {
            article.primingPointers.set(ev.pointerId, [ev.clientX, ev.clientY]);
        });
        article.addEventListener('pointermove', ev => {
            if (article.primingPointers.has(ev.pointerId)) {
                const [initialX, initialY] = article.primingPointers.get(ev.pointerId);
                const dx = ev.clientX - initialX;
                const dy = ev.clientY - initialY;
                // Dragging more than 50 px prevents the click action
                if (dx * dx + dy * dy > 50 * 50) {
                    article.primingPointers.delete(ev.pointerId);
                }
            }
        });
        article.addEventListener('pointerleave', ev => {
            article.primingPointers.delete(ev.pointerId);
        });
        article.addEventListener('pointerup', ev => {
            if (
                article.primingPointers.has(ev.pointerId) &&
                !article.deadzonePointers.has(ev.pointerId)
            ) {
                article.primingPointers.delete(ev.pointerId);
                window.location.href = article.getAttribute('data-link');
            }
        });
        article.addEventListener('keyup', ev => {
            if (ev.key === 'Enter') {
                window.location.href = article.getAttribute('data-link');
            }
        });
        if (!radiosSection) {
            return; // Skip to next article
        }
        radiosSection.style.touchAction = 'none'; // Handle scrolling manually
        let thumbnailElement = article.querySelector(':first-child');
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
        article.radios = Array.from(radiosSection.children);
        // When hovering over a radio button, the associated thumbnail is displayed
        // Otherwise the one associated with the checked button is displayed
        const setArticleLink = (radio) => {
            const product = article.getAttribute('data-product');
            const variant = radio.getAttribute('data-variant-suffix');
            article.setAttribute('data-link', 'product?id=' + product + '&variant=' + variant);
        };
        article.radios.forEach(radio => {
            radio.addEventListener('click', ev => {
                if (article.radios.length <= 5 || ev.isProgrammatic || ev.pointerId === -1) {
                    setArticleLink(radio);
                    displayThumbnail(radio);
                }
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
            if (radio.checked) {
                setArticleLink(radio);
            }
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
        // Up to 5 radio buttons can be displayed neatly without needing the scrolling functionality
        if (article.radios.length > 5) {
            const updateScrollFactor = (scrollLeft = null) => {
                radiosSection.scrollFactor = (scrollLeft || radiosSection.scrollLeft) / parseFloat(window.getComputedStyle(radiosSection).width);
            }
            updateScrollFactor();
            // When clicking on a section assign a moveHandler and an upHandler (if it doesn't have one already)
            radiosSection.addEventListener('pointerdown', ev => {
                if (!radiosSection.moveHandler) {
                    radiosSection.dragged = null;
                    radiosSection.moveHandler = ev => {
                        if (ev.pointerId === radiosSection.moveHandler.id) {
                            radiosSection.scrollLeft = radiosSection.moveHandler.initialScroll - ev.clientX + radiosSection.moveHandler.initialX;
                            updateScrollFactor();
                            // Any movement of more than 10 px is considered intentional,
                            // which means no click action should occur
                            if (Math.abs(ev.clientX - radiosSection.moveHandler.initialX) > 10) {
                                article.primingPointers.delete(ev.pointerId);
                                radiosSection.moveHandler.target = null;
                                radiosSection.dragged = ev.pointerId;
                            }
                        }
                    };
                    radiosSection.moveHandler.id = ev.pointerId;
                    radiosSection.moveHandler.initialX = ev.clientX;
                    radiosSection.moveHandler.initialScroll = radiosSection.scrollLeft;
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
                            if (radiosSection.dragged === ev.pointerId) {
                                ev.stopPropagation();
                            }
                        }
                    }
                    window.addEventListener('pointerup', upHandler);
                }
            });
            article.radios.forEach((radio, index) => {
                radio.addEventListener('click', ev => {
                    if (ev.isProgrammatic || ev.pointerId === -1) {
                        // Scroll to have the selected radio button in the center (or close to it)
                        const radio = article.radios[index];
                        const radioStyle = window.getComputedStyle(radio);
                        const radiosSectionStyle = window.getComputedStyle(radiosSection);
                        // To avoid having too many radio buttons hidden, and also remain consistent with the '5 policy',
                        // clamp the index so that, when clicking the first and second buttons, the third gets centered,
                        // and, similarly, when clicking the last and second-to-last buttons, the third-to-last gets centered
                        const clampedIndex = Math.min(Math.max(index, 2), article.radios.length - 3);
                        const offset = (clampedIndex - 2) * (parseFloat(radioStyle.width) + parseFloat(radiosSectionStyle.gap));
                        radiosSection.scrollTo({
                            top: 0,
                            left: offset,
                            behavior: 'smooth'
                        });
                        updateScrollFactor(offset);
                    } else {
                        // Ignore/disable all events triggered directly by the browser
                        ev.preventDefault();
                        ev.stopPropagation();
                    }
                });
            });
            // The scroll position might need to be updated when zooming or resizing
            // This is likely because of the font size breakpoints set by pico
            window.addEventListener('resize', () => {
                radiosSection.scrollTo({
                    top: 0,
                    left: radiosSection.scrollFactor * parseFloat(window.getComputedStyle(radiosSection).width),
                    behavior: 'smooth'
                });
            });
        }
        article.addEventListener('pointermove', ev => {
            const deadzone = 6 / window.devicePixelRatio;
            let firstRect = article.radios[0].getBoundingClientRect();
            let diameter = firstRect.width;
            let inDeadzone = ev.clientY >= firstRect.y - deadzone && ev.clientY <= firstRect.y + diameter + deadzone; // Rough y check
            if (inDeadzone) {
                let deadRadius = diameter / 2 + deadzone;
                let deadRadius2 = deadRadius * deadRadius;
                inDeadzone = article.radios.some(radio => {
                    let rect = radio.getBoundingClientRect();
                    // Rough x check
                    if (ev.clientX < rect.x - deadzone || ev.clientX > rect.x + diameter + deadzone) {
                        return false;
                    }
                    let dx = ev.clientX - (rect.x + diameter / 2);
                    let dy = ev.clientY - (rect.y + diameter / 2);
                    return dx * dx + dy * dy <= deadRadius2;
                });
            }
            if (inDeadzone) {
                article.style.cursor = 'default';
                article.deadzonePointers.add(ev.pointerId);
                article.setAttribute('data-hover-disabled', 'disabled');
            } else {
                article.style.cursor = 'pointer';
                article.deadzonePointers.delete(ev.pointerId);
                article.removeAttribute('data-hover-disabled');
            }
        });
    });
});
