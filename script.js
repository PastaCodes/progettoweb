window.addEventListener('load', () => {
    const noThumbnailTemplate = document.getElementById('no-thumbnail');
    document.querySelectorAll('article').forEach(article => {
        const radiosSection = article.querySelector('section:nth-child(2):not(:last-child)');
        if (!radiosSection)
            return;
        const thumbnailSection = article.querySelector('section:nth-child(1)');
        const displayThumbnail = (activeRadio) => {
            let img = thumbnailSection.querySelector('img');
            const thumbnail = activeRadio.getAttribute('data-thumbnail');
            if (thumbnail && !img)
                thumbnailSection.replaceChildren(img = document.createElement('img'));
            if (thumbnail) {
                img.src = thumbnail;
                img.loading = 'eager';
            } else if (img)
                thumbnailSection.innerHTML = noThumbnailTemplate.innerHTML;
        }
        const children = Array.from(radiosSection.children);
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
        if (children.length > 5) {
            radiosSection.addEventListener('pointerdown', ev => {
                if (!radiosSection.moveHandler) {
                    radiosSection.moveHandler = ev => {
                        if (ev.pointerId === radiosSection.moveHandler.id) {
                            radiosSection.scrollBy(radiosSection.moveHandler.lastX - ev.clientX, 0);
                            radiosSection.moveHandler.lastX = ev.clientX;
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
            children.forEach((radio, index) => {
                radio.addEventListener('click', ev => {
                    if (ev.isProgrammatic) {
                        const radioStyle = window.getComputedStyle(radio);
                        const radiosSectionStyle = window.getComputedStyle(radiosSection);
                        const offset = (index - 2) * (parseFloat(radioStyle.width) + parseFloat(radiosSectionStyle.gap));
                        radiosSection.scrollTo({
                            top: 0,
                            left: offset,
                            behavior: 'smooth'
                        });
                    } else {
                        ev.preventDefault();
                        ev.stopPropagation();
                    }
                });
            });
        }
    });
});
