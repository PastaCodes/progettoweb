import { createCookie, getCookie, deleteCookie } from "./cookie.js";

const CART_COOKIE_NAME = 'cart';

// variant_id = null if product has no variant
function modifyCart(product_id, variant_id, quantity = 1) {
    if (!product_id || !quantity) {
        return;
    }
    // Get the cart info from the cookie
    let cart = JSON.parse(getCookie(CART_COOKIE_NAME) || '{}');
    // Ensure product exists in cart
    if (!cart[product_id]) {
        cart[product_id] = variant_id ? {} : 0;
    }
    // Handling product variants
    if (variant_id) {
        // Ensure variant exists in product
        if (!cart[product_id][variant_id]) {
            cart[product_id][variant_id] = 0;
        }
        // Modify the quantity for the variant
        cart[product_id][variant_id] += quantity;
        // Remove variant if variant no longer in cart 
        if (cart[product_id][variant_id] <= 0) {
            delete cart[product_id][variant_id];
            // Remove product if no variants left
            if (Object.keys(cart[product_id]).length === 0) {
                delete cart[product_id];
            }
        }
    } else {
        // Modify the quantity for the product if no variant is provided
        cart[product_id] += quantity;
        // Remove product if product no longer in cart
        if (cart[product_id] <= 0) {
            delete cart[product_id];
        }
    }
    // Clear the cart if empty 
    if (Object.keys(cart).length <= 0)
        clearCart();
    else
        createCookie(CART_COOKIE_NAME, JSON.stringify(cart), 30 * 24 * 60 * 60 * 1000); // 30 day long cookie
}

function clearCart() {
    deleteCookie(CART_COOKIE_NAME);
}

// ????? Why are modules like this
window.modifyCart = modifyCart;
window.clearCart = clearCart;
