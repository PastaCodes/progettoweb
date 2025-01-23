<?php
require_once '../classes/Product.php';
require_once '../classes/ProductVariant.php';
require_once '../util/format.php';
require_once '../util/files.php';

$product = Product::from($_GET['id'], $_GET['variant'] ?? null);
$product->fetch_all_details();
?>
        <template>
            <figure>
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                    <use href="assets/nothing.svg#nothing"></use>
                </svg>
                <figcaption>No image available</figcaption>
            </figure>
        </template>
        <main>
            <section>
<?php if ($product->thumbnail() === null): ?>
                <figure>
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                        <use href="assets/nothing.svg#nothing"></use>
                    </svg>
                    <figcaption>No image available</figcaption>
                </figure>
<?php else: ?>
                <img src="<?= $product->thumbnail()->file ?>" loading="lazy" alt="<?= $product->thumbnail()->alt_text ?>">
<?php endif ?>
<?php if (!empty($product->base->variants)): ?>
                <fieldset>
<?php foreach ($product->base->variants as $variant): ?>
                    <input type="radio" name="variant" <?= $variant->to_radio_attributes(selected_suffix: $product->variant->code_suffix, include_price: true) ?>>
<?php endforeach ?>
                </fieldset>
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
            </section>
        </main>
