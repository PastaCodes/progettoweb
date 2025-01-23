<?php
require_once '../util/format.php';
require_once '../classes/Cart.php';

$cart = Cart::load_from_cookie();
if (!empty($cart->entries)) {
    $cart->fetch_details();
}
?>
        <main>
            <section>
                <h1>Your cart</h1>
<?php foreach ($cart->entries as $entry): ?>
                <div data-unit-price="<?= number_format($entry->unit_price(), 2) ?>">
<?php if ($entry instanceof ProductEntry): ?>
<?php if ($entry->product->thumbnail() === null): ?>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                            <use href="assets/nothing.svg#nothing"></use>
                        </svg>
                        <p>No image available</p>
                    </div>
<?php else: ?>
                    <img src="<?= $entry->product->thumbnail()->file ?>" loading="lazy" alt="<?= $entry->product->thumbnail()->alt_text ?>">
<?php endif ?>
<?php else: ?>
                    <div>Piedoni</div>
<?php endif ?>
                    <a <?= $entry->to_link_attributes() ?>><?= $entry->display_name() ?> <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label=""><use href="assets/link.svg#link"></use></svg></a>
<?php if ($entry->variant_display_name() !== null): ?>
                    <p><?= $entry->variant_display_name() ?></p>
<?php endif ?>
                    <p><?= format_price($entry->entry_price()) ?></p>
                    <p><?= format_price($entry->unit_price()) ?>/pc</p>
                    <button title="Remove from cart">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                            <use href="assets/remove.svg#remove"></use>
                        </svg>
                    </button>
                    <div role="group">
                        <button <?= $entry->quantity === 1 ? 'disabled' : ''?>>-</button>
                        <input type="number" name="quantity" inputmode="numeric" min="1" max="99" value="<?= $entry->quantity ?>">
                        <button <?= $entry->quantity === 99 ? 'disabled' : ''?>>+</button>
                    </div>
                </div>
<?php endforeach ?>
            </section>
        </main>
