document.addEventListener('DOMContentLoaded', () => {
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
                    thumbnailSection.replaceChildren();
                    thumbnailSection.appendChild(img);
                } else
                    thumbnailSection.innerHTML = noThumbnailTemplate.innerHTML;
            }
            let children = Array.from(radiosSection.children);
            children.forEach(radio => {
                radio.addEventListener('mouseover', () => displayThumbnail(radio));
                radio.addEventListener('mouseout', () => displayThumbnail(radiosSection.querySelector(':checked')));
            });
            if (children.length > 5) {
                let adjustRadios = (selectedIndex) => {
                    let clampedIndex = Math.min(Math.max(selectedIndex, 2), children.length - 3)
                    let offset = (children.length - 1) / 2 - clampedIndex;
                    children.forEach(radio => {
                        radio.style.setProperty('transform', `translateX(calc(${offset} * (1.25rem + var(--radio-spacing))))`);
                    });
                }
                adjustRadios(0);
                children.forEach((radio, index) => {
                    radio.addEventListener('click', () => adjustRadios(index));
                });
            }
        }
    });
})
window.addEventListener('load', () => {
    document.querySelectorAll('[type="radio"]').forEach(radio => {
        // For silly little browsers that do not support attr styling
        radio.style.setProperty('--radio-color', radio.getAttribute('data-color'));
    })
})