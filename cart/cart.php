<?php
require_once '../util/db.php';
require_once '../util/format.php';
require_once '../classes/Cart.php';

$cart = Cart::load_from_cookie();
if (!empty($cart->entries)) {
    $cart->fetch_details();
}
?>
        <main>
            <h1>Your cart</h1>
<?php foreach ($cart->entries as $entry): ?>
            <div>
<?php if ($entry->product->thumbnail() === null): ?>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-label="No image available">
                        <use href="assets/nothing.svg#root"></use>
                    </svg>
                    <p>No image available</p>
                </div>
<?php else: ?>
                <img src="<?= $entry->product->thumbnail()->file ?>" loading="lazy" alt="<?= $entry->product->thumbnail()->alt_text ?>">
<?php endif ?>
                <a href="product?<?= $entry->product->to_url_params() ?>"><?= $entry->product->base->display_name ?> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><use href="assets/link.svg#root"></use></svg></a>
<?php if ($entry->product->variant !== null): ?>
                <p><?= $entry->product->variant->display_name ?></p>
<?php endif ?>
                <button>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" aria-label="Remove from cart">
                        <use href="assets/remove.svg#root"></use>
                    </svg>
                </button>
                <div role="group">
                    <button <?= $entry->quantity === 1 ? 'disabled' : ''?>>-</button>
                    <input name="quantity" type="number" min="1" max="99" data-unit-price="<?= $entry->product->price ?>" data-base-code="<?= $entry->product->base->code_name?>"<?= $entry->product->variant ? ' data-variant-suffix="' . $entry->product->variant->code_suffix . '"' : '' ?> value="<?= $entry->quantity ?>">
                    <button <?= $entry->quantity === 99 ? 'disabled' : ''?>>+</button>
                </div>
                <p>&euro; <?= format_price($entry->entry_price()) ?></p>
                <p>&euro; <?= format_price($entry->product->price) ?>/pc</p>
            </div>
<?php endforeach ?>
        </main>
