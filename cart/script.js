// Wait for the style to be rendered
window.addEventListener('load', () => {
    // Ensure new value for inputs stays in range
    const valueClamp = (input) => {
        let val = parseInt(input.value);
        const max = parseInt(input.max);
        const min = parseInt(input.min);
        if (val > max) {
            val = max;
        } else if (val < min) {
            val = min;
        }
        input.value = val;
        return val;
    };
    const setInputValue = (input, value) => {
        input.value = value;
        return valueClamp(input);
    };
    // For each product within the cart
    document.querySelectorAll('article').forEach(cartProductSection => {
        // Get product data
        const inputProductQty = cartProductSection.querySelector('input');
        const productBaseCode = inputProductQty.getAttribute('data-base-code');
        const productVariantSuffix = inputProductQty.getAttribute('data-variant-suffix');
        // Get buttons
        const btnDelete = cartProductSection.querySelector('button:nth-last-child(4)');
        const btnDecrement = cartProductSection.querySelector('button:first-child');
        const btnIncrement = cartProductSection.querySelector('button:last-child');
        // Setup listeners
        // Changing input value manually
        inputProductQty.addEventListener('change', (evt) => {
            const newValue = evt.target.value;
            const actualValue = valueClamp(inputProductQty);
            setCart(productBaseCode, productVariantSuffix, actualValue);
        });
        // Delete button
        btnDelete.addEventListener('click', () => {
            // Set product quantity to 0
            inputProductQty.value = 0;
            setCart(productBaseCode, productVariantSuffix, 0);
            // Disable all buttons
            inputProductQty.disabled = true;
            btnDelete.disabled = true;
            btnDecrement.disabled = true;
            btnIncrement.disabled = true;
            // Little animation for fun
            cartProductSection.style.transition = "transform 0.1s ease-out"
            cartProductSection.style.transform = "translateX(-2vw)";
            setTimeout(() => {
                cartProductSection.style.transition = "transform 0.3s ease-in"
                cartProductSection.style.transform = "translateX(100vw)";
            }, 100);
            // Remove the element from the html after the animation
            setTimeout(() => {
                cartProductSection.remove();
            }, 400);
        });
        // Decrement product quantity
        btnDecrement.addEventListener('click', () => {
            const actualValue = setInputValue(inputProductQty, parseInt(inputProductQty.value) - 1);
            setCart(productBaseCode, productVariantSuffix, actualValue);
        });
        // Increment product quantity
        btnIncrement.addEventListener('click', () => {
            const actualValue = setInputValue(inputProductQty, parseInt(inputProductQty.value) + 1);
            setCart(productBaseCode, productVariantSuffix, actualValue);
        });
    });
});
