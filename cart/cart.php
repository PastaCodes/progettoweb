<?php
// Check if vendor
if ($_SESSION['vendor'] ?? false) {
    header('Location: ../shop');
    exit();
}

require_once '../util/format.php';
require_once '../classes/Cart.php';

$cart = Cart::load_from_cookie();
if (!empty($cart->entries)) {
    $cart->fetch_details();
}
?>
        <template>
            <p>There are no items in your cart. <a href="shop">Continue shopping</a>.</p>
        </template>
        <main>
            <section>
                <h1>Your cart</h1>
<?php if (empty($cart->entries)): ?>
                <p>There are no items in your cart. <a href="shop">Continue shopping</a>.</p>
<?php else: ?>
                <ul>
<?php foreach ($cart->entries as $entry): ?>
                    <li data-unit-price="<?= number_format($entry->unit_price(), 2, thousands_separator: '') ?>">
<?php if ($entry instanceof ProductEntry): ?>
<?php if ($entry->product->thumbnail() === null): ?>
                        <figure>
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                                <use href="assets/nothing.svg#nothing"></use>
                            </svg>
                            <figcaption>No image available</figcaption>
                        </figure>
<?php else: ?>
                        <img src="<?= $entry->product->thumbnail()->file ?>" loading="lazy" alt="<?= $entry->product->thumbnail()->alt_text ?>">
<?php endif ?>
<?php else: /* BundleEntry */ ?>
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                            <use href="assets/bundle.svg#bundle"></use>
                        </svg>
<?php endif ?>
                        <a <?= $entry->to_link_attributes() ?>><?= $entry->display_name() ?> <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label=""><use href="assets/link.svg#link"></use></svg></a>
<?php if ($entry->variant_display_name() !== null): ?>
                        <p><?= $entry->variant_display_name() ?></p>
<?php endif ?>
<?php if ($entry instanceof BundleEntry): ?>
                        <p>Includes: <?= implode(', ', array_map(fn($product) => $product->base->display_name, $entry->bundle->products)) ?>.</p>
<?php endif ?>
                        <p><?= format_price($entry->entry_price()) ?></p>
                        <p><?= format_price($entry->unit_price()) ?>/pc</p>
                        <button title="Remove from cart">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                                <use href="assets/remove.svg#remove"></use>
                            </svg>
                        </button>
                        <label for="<?= format_entry_code($entry) ?>-quantity" hidden="hidden">Select the quantity</label>
                        <fieldset role="group">
                            <button <?= $entry->quantity === 1 ? 'disabled' : ''?>>-</button>
                            <input id="<?= format_entry_code($entry) ?>-quantity" type="number" name="quantity" inputmode="numeric" min="1" max="99" value="<?= $entry->quantity ?>">
                            <button <?= $entry->quantity === 99 ? 'disabled' : ''?>>+</button>
                        </fieldset>
                    </li>
<?php endforeach ?>
                </ul>
                <p>Subtotal: <?= format_price($cart->get_total()) ?></p>
                <a role="button" href="checkout">Checkout</a>
<?php endif ?>
            </section>
        </main>
