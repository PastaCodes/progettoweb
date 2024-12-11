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
        if (children.length > 5)
            children.forEach((radio, index) => {
                radio.addEventListener('click', () => {
                    const radioStyle = window.getComputedStyle(radio);
                    const radiosSectionStyle = window.getComputedStyle(radiosSection);
                    const offset = (index - 2) * (parseFloat(radioStyle.width) + parseFloat(radiosSectionStyle.gap));
                    radiosSection.scrollTo({
                        top: 0,
                        left: offset,
                        behavior: 'smooth'
                    });
                });
            });
    });
});
