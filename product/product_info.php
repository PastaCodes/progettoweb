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
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                    <use href="assets/nothing.svg#nothing"></use>
                </svg>
                <p>No image available</p>
            </div>
        </template>
        <main>
<?php if ($product === null): ?>
            <h1>Product not found</h1>
<?php else: ?>
<?php if ($product->thumbnail() === null): ?>
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                    <use href="assets/nothing.svg#nothing"></use>
                </svg>
                <p>No image available</p>
            </div>
<?php else: ?>
            <img src="<?= $product->thumbnail()->file ?>" loading="lazy" alt="<?= $product->thumbnail()->alt_text ?>">
<?php endif ?>
<?php if (!empty($product->base->variants)): ?>
            <div>
<?php foreach ($product->base->variants as $variant): ?>
                <input type="radio" name="variant" <?= $variant->to_radio_attributes(selected_suffix: $product->variant->code_suffix, include_price: true) ?>>
<?php endforeach ?>
            </div>
<?php endif ?>
            <h1><?= $product->base->display_name ?></h1>
            <p><?= $product->base->short_description ?></p>
<?php if ($product->variant !== null): ?>
            <p><?= $product->variant->display_name ?></p>
<?php endif ?>
            <p><?= format_price($product->price) ?></p>
<?php if ($product->base->is_standalone): ?>
            <button data-product-name="<?= $product->base->code_name ?>"<?php if ($product->variant !== null): ?> data-variant-suffix="<?= $product->variant->code_suffix ?>"<?php endif ?>>Add to cart</button>
<?php endif ?>
<?php endif ?>
        </main>
