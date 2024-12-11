window.addEventListener('load', () => {
    let noThumbnailTemplate = document.getElementById('no-thumbnail');
    document.querySelectorAll('article').forEach(article => {
        let radiosSection = article.querySelector('section:nth-child(2):not(:last-child)');
        if (radiosSection) {
            let thumbnailSection = article.querySelector('section:nth-child(1)');
            let displayThumbnail = (activeRadio) => {
                let thumbnail = activeRadio.getAttribute('data-thumbnail');
                if (thumbnail) {
                    let img = document.createElement('img');
                    img.src = thumbnail;
                    img.loading = 'lazy';
                    thumbnailSection.replaceChildren();
                    thumbnailSection.appendChild(img);
                } else
                    thumbnailSection.innerHTML = noThumbnailTemplate.innerHTML;
            }
            let children = Array.from(radiosSection.children);
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
                        let radioStyle = window.getComputedStyle(radio);
                        let radiosSectionStyle = window.getComputedStyle(radiosSection);
                        let offset = (index - 2) * (parseFloat(radioStyle.width) + parseFloat(radiosSectionStyle.gap));
                        radiosSection.scrollTo({
                            top: 0,
                            left: offset,
                            behavior: 'smooth'
                        });
                    });
                });
        }
    });
});
