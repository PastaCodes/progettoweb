import { createCookie, getCookie, deleteCookie } from "./cookie.js";

const CART_COOKIE_NAME = 'shopping_cart';

function modifyCart(product_id, variant_id, quantity = 1) {
    let cart = JSON.parse(getCookie(CART_COOKIE_NAME) || '{}');
    // Initialize the product if it does not exist in the cart
    if (!cart[product_id]) {
        cart[product_id] = {};
    }
    // Initialize the product variant if it does not exist in the cart
    if (!cart[product_id][variant_id]) {
        cart[product_id][variant_id] = 0;
    }
    cart[product_id][variant_id] += quantity;
    // Erase the product from the cart if the quantity reaches 0
    if (cart[product_id][variant_id] <= 0) {
        delete cart[product_id];
    }
    if (cart.keys()) {
        // Clear the cart if there is nothing in it (erase the cookie)
        clearCart();
    } else {
        // Set the cookie with the new cart to be 30 days long
        createCookie(CART_COOKIE_NAME, JSON.stringify(cart), 30 * 24 * 60 * 60 * 1000);
    }
}

function clearCart() {
    deleteCookie(CART_COOKIE_NAME);
}

// ????? Why are modules like this
window.modifyCart = modifyCart;
window.clearCart = clearCart;
