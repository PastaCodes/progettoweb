import { createCookie, getCookie, deleteCookie } from "./cookie.js";

const CART_COOKIE_NAME = 'cart';

// variantId = null if product has no variant
function modifyCart(productId, variantId, quantity = 1) {
    if (!productId || !quantity) {
        return;
    }
    // Get the cart info from the cookie
    let cart = JSON.parse(getCookie(CART_COOKIE_NAME) || '{}');
    // Ensure product exists in cart
    if (!cart[productId]) {
        cart[productId] = variantId ? {} : 0;
    }
    // Handling product variants
    if (variantId) {
        // Ensure variant exists in product
        if (!cart[productId][variantId]) {
            cart[productId][variantId] = 0;
        }
        // Modify the quantity for the variant
        cart[productId][variantId] += quantity;
        // Remove variant if variant no longer in cart 
        if (cart[productId][variantId] <= 0) {
            delete cart[productId][variantId];
            // Remove product if no variants left
            if (Object.keys(cart[productId]).length === 0) {
                delete cart[productId];
            }
        }
    } else {
        // Modify the quantity for the product if no variant is provided
        cart[productId] += quantity;
        // Remove product if product no longer in cart
        if (cart[productId] <= 0) {
            delete cart[productId];
        }
    }
    // Clear the cart if empty 
    if (Object.keys(cart).length <= 0) {
        clearCart();
    } else {
        createCookie(CART_COOKIE_NAME, JSON.stringify(cart), 30 * 24 * 60 * 60 * 1000); // 30 day long cookie
    }
}

function clearCart() {
    deleteCookie(CART_COOKIE_NAME);
}

// ????? Why are modules like this
window.modifyCart = modifyCart;
window.clearCart = clearCart;
