document.addEventListener('DOMContentLoaded', () => {
    // Hide all variants
    document.querySelectorAll(`tr[data-parent]`).forEach(elt => {
        elt.style.display = 'none';
    });
    // Toggle variants on button press
    document.querySelectorAll("button[data-show]").forEach(showVariantButton => {
        const dataShow = showVariantButton.getAttribute('data-show');
        showVariantButton.addEventListener('click', () => {
            showVariantButton.innerHTML = showVariantButton.innerHTML === 'V' ? '^' : 'V';
            document.querySelectorAll(`tr[data-parent=${dataShow}]`).forEach(elt => {
                elt.style.display = elt.style.display === 'none' ? '' : 'none';
            });
        });
    });
});
