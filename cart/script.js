function priceFormat(number, decimalSeparator, thousandSeparator) {
    // Convert number to string with 2 decimal places
    const parts = number.toFixed(2).split('.');
    const integerPart = parts[0];
    const decimalPart = parts[1];
    // Add custom thousand separators
    const withThousandSeparators = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);
    // Combine integer and decimal parts with the custom decimal separator
    return withThousandSeparators + decimalSeparator + decimalPart;
}

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
        if (Number.isInteger(value)) {
            input.value = value;
            return valueClamp(input);
        }
        const val = parseInt(input.min);
        input.value = val;
        return val;
    };
    // For each product within the cart
    document.querySelectorAll('main > div').forEach(cartProductSection => {
        // Get product data
        const inputProductQty = cartProductSection.querySelector('input');
        const productBaseCode = inputProductQty.getAttribute('data-base-code');
        const productVariantSuffix = inputProductQty.getAttribute('data-variant-suffix');
        // Get price related stuff
        const fullCartProductPriceElt = cartProductSection.querySelector('p:nth-last-child(2)');
        const productUnitPrice = parseFloat(inputProductQty.getAttribute('data-unit-price'));
        // Get buttons
        const btnDelete = cartProductSection.querySelector('button:nth-last-child(4)');
        const btnDecrement = cartProductSection.querySelector('button:first-child');
        const btnIncrement = cartProductSection.querySelector('button:last-child');
        // Functions to streamline updating a product's values
        const updatePrice = (quantity) => {
            const newPrice = productUnitPrice * quantity;
            fullCartProductPriceElt.innerHTML = `&euro; ${priceFormat(newPrice, ',', '.')}`;
        };
        const updateIncDecButtons = (value) => {
            btnDecrement.disabled = '';
            btnIncrement.disabled = '';
            if (value == parseInt(inputProductQty.max)) {
                btnIncrement.disabled = true;   
            } 
            if (value == parseInt(inputProductQty.min)) {
                btnDecrement.disabled = true;   
            }
        };
        const updateProductData = (newValue) => {
            const actualValue = setInputValue(inputProductQty, newValue);
            updatePrice(actualValue);
            setCart(productBaseCode, productVariantSuffix, actualValue);
            updateIncDecButtons(actualValue); 
        };
        // Setup listeners
        // Changing input value manually
        inputProductQty.addEventListener('change', (evt) => {
            const newValue = evt.target.value;
            updateProductData(parseInt(newValue));
        });
        // Delete button
        btnDelete.addEventListener('click', () => {
            // Set product quantity to 0
            setCart(productBaseCode, productVariantSuffix, 0);
            // Disable all buttons
            inputProductQty.disabled = true;
            btnDelete.disabled = true;
            btnDecrement.disabled = true;
            btnIncrement.disabled = true;
            // Little animation for fun
            cartProductSection.style.animation = 'productRemove 0.8s ease-in-out';
            // Remove the element from the html after the animation
            cartProductSection.addEventListener('animationend', () => cartProductSection.remove());
        });
        // Decrement product quantity
        btnDecrement.addEventListener('click', () => {
            updateProductData(parseInt(inputProductQty.value) - 1);
        });
        // Increment product quantity
        btnIncrement.addEventListener('click', () => {
            updateProductData(parseInt(inputProductQty.value) + 1);
        });
    });
});
