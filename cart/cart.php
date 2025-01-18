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
            <section>
                <h1>Your cart</h1>
<?php foreach ($cart->entries as $entry): ?>
                <article>
<?php if ($entry->product->thumbnail() === null): ?>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" title="No image available" aria-label="No image available">
                            <use href="assets/nothing.svg#root"></use>
                        </svg>
                        <p>No image available</p>
                    </div>
<?php else: ?>
                    <img src="<?= $entry->product->thumbnail()->file ?>" loading="lazy" alt="<?= $entry->product->thumbnail()->alt_text ?>">
<?php endif ?>
                    <p><a href="product?<?= $entry->product->to_url_params() ?>"><?= $entry->product->base->display_name ?> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><use href="assets/link.svg#root"></use></svg></a></p>
<?php if ($entry->product->variant !== null): ?>
                    <p><?= $entry->product->variant->display_name ?></p>
<?php endif ?>
                    <button>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <use href="assets/remove.svg#root"></use>
                        </svg>
                    </button>
                    <div role="group">
                        <button>-</button>
                        <input name="quantity" type="number" min="1" max="99" value="<?= $entry->quantity ?>">
                        <button>+</button>
                    </div>
                    <p>&euro; <?= format_price($entry->entry_price()) ?></p>
                    <p>&euro; <?= format_price($entry->product->price) ?>/pc</p>
                </article>
<?php endforeach ?>
            </section>
        </main>
