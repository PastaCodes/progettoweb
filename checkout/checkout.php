<?php
// Check if vendor
if ($_SESSION['vendor'] ?? false) {
    header('Location: ../shop');
    exit();
}

require_once '../util/format.php';
require_once '../classes/Cart.php';

$cart = Cart::load_from_cookie();
if (empty($cart->entries)) {
    header('Location: ../cart');
    exit();
}
$cart->fetch_details();
?>
        <main>
            <section>
                <p><a href="cart">Go back to your cart</a></p>
                <h1>Review your order</h1>
                <ul>
<?php foreach ($cart->entries as $entry): ?>
                    <li>
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
                        <p>Quantity: <?= $entry->quantity ?></p>
                    </li>
<?php endforeach ?>
                </ul>
                <p>Total: <?= format_price($cart->get_total()) ?></p>
                <p>Tax included</p>
                <p>Free shipping</p>
            </section>
            <form action="api" method="POST">
                <fieldset>
                    <legend>Shipment details</legend>
                    <label for="shipping-address">Shipping address</label>
                    <input id="shipping-address" type="text" disabled="disabled" value="Via Cesare Pavese, 50, 47521 Cesena FC">
                </fieldset>
                <fieldset>
                    <legend>Payment details</legend>
                    <label for="card-number">
                        Card number
                        <input id="card-number" type="text" disabled="disabled" value="0000 0000 0000 0000">
                    </label>
                    <label for="cvv">
                        CVV
                        <input id="cvv" type="text" disabled="disabled" value="000">
                    </label>
                    <label for="expiry-date">
                        Expiry date
                        <input id="expiry-date" type="text" disabled="disabled" value="01/00">
                    </label>
                </fieldset>
<?php if (isset($_SESSION['username'])): ?>
                <input type="submit" name="order" value="Confirm order">
<?php else: ?>
                <p><a href="login">Login to proceed</a></p>
                <input type="submit" name="order" value="Confirm order" disabled="disabled">
<?php endif ?>
            </form>
        </main>
