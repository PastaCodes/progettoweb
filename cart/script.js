import { formatPrice } from '../scripts/util.js';
import { setQuantityInCart } from '../scripts/cart.js';

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
        return input.value = input.lastValidValue;
    };
    let cartProductSections = Array.from(document.querySelectorAll('main > section > ul > li'));
    const subtotalDisplay = document.querySelector('main > section > p:nth-last-child(2)');
    const updateSubtotal = () => {
        let subtotal = 0;
        cartProductSections.forEach(cartProductSection => {
            subtotal += cartProductSection.productUnitPrice * cartProductSection.quantity;
        });
        subtotalDisplay.innerHTML = 'Subtotal: ' + formatPrice(subtotal);
    }
    // For each product within the cart
    cartProductSections.forEach(cartProductSection => {
        // Get product data
        const inputProductQty = cartProductSection.querySelector('input');
        // Get price related stuff
        const fullCartProductPriceElt = cartProductSection.querySelector('p:nth-last-of-type(2)');
        cartProductSection.productUnitPrice = parseFloat(cartProductSection.getAttribute('data-unit-price'));
        cartProductSection.quantity = parseInt(inputProductQty.value);
        // Get buttons
        const btnDelete = cartProductSection.querySelector('button:nth-last-of-type(1)');
        const btnDecrement = cartProductSection.querySelector('button:first-child');
        const btnIncrement = cartProductSection.querySelector('button:last-child');
        // Functions to streamline updating a product's values
        const updatePrice = () => {
            const newPrice = cartProductSection.productUnitPrice * cartProductSection.quantity;
            fullCartProductPriceElt.innerHTML = formatPrice(newPrice);
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
            cartProductSection.quantity = actualValue;
            updatePrice();
            setQuantityInCart(cartProductSections.indexOf(cartProductSection), actualValue);
            updateIncDecButtons(actualValue); 
        };
        // Setup listeners
        // Changing input value manually
        inputProductQty.addEventListener('change', () => {
            updateProductData(parseInt(inputProductQty.value));
            updateSubtotal();
        });
        inputProductQty.lastValidValue = inputProductQty.value;
        inputProductQty.addEventListener('input', () => {
            const newValue = inputProductQty.value;
            if (!/^([1-9][0-9]?)?$/.test(newValue)) {
                // Revert to last valid value with the associated selection
                inputProductQty.value = inputProductQty.lastValidValue;
                inputProductQty.setSelectionRange(...Object.values(inputProductQty.lastSelection));
            } else if (newValue !== '') {
                inputProductQty.lastValidValue = newValue;
            }
        });
        inputProductQty.addEventListener('beforeinput', () => {
            inputProductQty.lastSelection = {
                start: inputProductQty.selectionStart,
                end: inputProductQty.selectionEnd,
                direction: inputProductQty.selectionDirection,
            };
        });
        // Delete button
        btnDelete.addEventListener('click', () => {
            const index = cartProductSections.indexOf(cartProductSection);
            // Set product quantity to 0
            setQuantityInCart(index, 0);
            cartProductSections.splice(index, 1);
            if (cartProductSections.length === 0) {
                document.querySelectorAll('main > section > :not(h1, ul)').forEach(el => el.remove());
            }
            updateSubtotal();
            // Disable all buttons
            inputProductQty.disabled = true;
            btnDelete.disabled = true;
            btnDecrement.disabled = true;
            btnIncrement.disabled = true;
            const height = cartProductSection.getBoundingClientRect().height;
            cartProductSection.style.setProperty('--initial-height', height + 'px');
            // Little animation for fun
            cartProductSection.style.animation = 'product-remove 0.4s ease-in-out';
            // Remove the element from the html after the animation
            cartProductSection.addEventListener('animationend', () => {
                cartProductSection.remove();
                if (cartProductSections.length === 0) {
                    document.querySelector('main > section > ul').outerHTML = document.querySelector('template').innerHTML;
                }
            });
        });
        // Decrement product quantity
        btnDecrement.addEventListener('click', () => {
            updateProductData(parseInt(inputProductQty.value) - 1);
            updateSubtotal();
        });
        // Increment product quantity
        btnIncrement.addEventListener('click', () => {
            updateProductData(parseInt(inputProductQty.value) + 1);
            updateSubtotal();
        });
    });
});
