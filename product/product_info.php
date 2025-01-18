<?php
require_once '../util/db.php';
require_once '../classes/Product.php';
require_once '../classes/ProductVariant.php';
require_once '../util/format.php';
require_once '../util/files.php';

$product = Product::from($_GET['id'], $_GET['variant'] ?? null);
$product->fetch_all_details();
?>
        <template id="no-thumbnail">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-label="No image available">
                <use href="assets/nothing.svg#root"></use>
            </svg>
            <span>No image available</span>
        </template>
        <main>
<?php if ($product === null): ?>
            <section>
                <h1>Product not found</h1>
            </section>
<?php else: ?>
            <section>
<?php if ($product->thumbnail() === null): ?>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-label="No image available">
                    <use href="assets/nothing.svg#root"></use>
                </svg>
                <span>No image available</span>
<?php else: ?>
                <img src="<?= $product->thumbnail()->file ?>" loading="lazy" alt="<?= $product->thumbnail()->alt_text ?>">
<?php endif ?>
            </section>
<?php if (!empty($product->base->variants)): ?>
            <section>
<?php foreach ($product->base->variants as $variant): ?>
                <input type="radio" name="variant" <?= $variant->to_radio_attributes(selected_suffix: $product->variant->code_suffix) ?>>
<?php endforeach ?>
            </section>
<?php endif ?>
            <section>
                <h1><?= $product->base->display_name ?></h1>
                <p><?= $product->base->short_description ?></p>
            </section>
<?php if ($product->base->is_standalone): ?>
            <section>
                <button>Add to cart</button>
            </section>
<?php endif ?>
<?php endif ?>
        </main>
