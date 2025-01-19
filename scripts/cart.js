import { createCookie, getCookie, deleteCookie } from "./cookie.js";

const CART_COOKIE_NAME = 'cart';

function setCart(productId, variantId, quantity) {
    if (!productId || quantity == null) {
        return;
    }
    // Get the cart info from the cookie
    let cart = JSON.parse(getCookie(CART_COOKIE_NAME) || '{}');
    if (quantity <= 0) {
        // Remove the product or variant if quantity is <= 0
        if (variantId) {
            if (cart[productId]) {
                delete cart[productId][variantId];
                // Remove product if no variants left
                if (Object.keys(cart[productId]).length === 0) {
                    delete cart[productId];
                }
            }
        } else {
            delete cart[productId];
        }
    } else {
        // Set the quantity for the product or variant
        if (variantId) {
            if (!cart[productId]) {
                cart[productId] = {};
            }
            cart[productId][variantId] = quantity;
        } else {
            cart[productId] = quantity;
        }
    }
    // Save or clear the cart depending on its state
    if (Object.keys(cart).length === 0) {
        clearCart();
    } else {
        createCookie(CART_COOKIE_NAME, JSON.stringify(cart), 30 * 24 * 60 * 60 * 1000);
    }
}

function modifyCart(productId, variantId, quantity = 1) {
    if (!productId || quantity == null) {
        return;
    }
    // Get the cart info from the cookie
    const cart = JSON.parse(getCookie(CART_COOKIE_NAME) || '{}');
    if (variantId) {
        // Modify the cart for a specific variant
        modifyVariantInCart(cart, productId, variantId, quantity);
    } else {
        // Modify the cart for a product without variants
        modifyProductInCart(cart, productId, quantity);
    }
    // Clear the cart if empty, otherwise save it
    if (Object.keys(cart).length === 0) {
        clearCart();
    } else {
        createCookie(CART_COOKIE_NAME, JSON.stringify(cart), 30 * 24 * 60 * 60 * 1000);
    }
}

function modifyVariantInCart(cart, productId, variantId, quantity) {
    // Ensure product exists in cart
    if (!cart[productId]) {
        cart[productId] = {};
    }
    // Modify the quantity for the variant
    cart[productId][variantId] = (cart[productId][variantId] || 0) + quantity;
    // Remove variant if no longer in cart
    if (cart[productId][variantId] <= 0) {
        delete cart[productId][variantId];
        // Remove product if no variants left
        if (Object.keys(cart[productId]).length === 0) {
            delete cart[productId];
        }
    }
}

function modifyProductInCart(cart, productId, quantity) {
    // Modify the quantity for the product
    cart[productId] = (cart[productId] || 0) + quantity;
    // Remove product if no longer in cart
    if (cart[productId] <= 0) {
        delete cart[productId];
    }
}

function clearCart() {
    // Delete the cart cookie
    deleteCookie(CART_COOKIE_NAME);
}

// ????? Why are modules like this
window.setCart = setCart;
window.modifyCart = modifyCart;
