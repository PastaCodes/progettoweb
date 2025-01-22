document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('main > section > div').forEach(article => {
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
            if (article.primingPointers.delete(ev.pointerId)) {
                window.location.href = article.getAttribute('data-link');
            }
        });
        article.addEventListener('keyup', ev => {
            if (ev.key === 'Enter') {
                window.location.href = article.getAttribute('data-link');
            }
        });
    });
});
