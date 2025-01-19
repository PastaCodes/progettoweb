<?php
require_once 'util/db.php';
require_once 'classes/Product.php';
require_once 'classes/ProductVariant.php';
require_once 'util/format.php';

$products = Product::fetch_products();
?>
        <template id="no-thumbnail">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-label="No image available">
                    <use href="assets/nothing.svg#root"></use>
                </svg>
                <p>No image available</p>
            </div>
        </template>
        <main>
<?php foreach ($products as $product): ?>
            <div data-product="<?= $product->base->code_name ?>" data-link="product?<?= $product->to_url_params() ?>">
<?php if ($product->thumbnail() !== null): ?>
                <img src="<?= $product->thumbnail()->file ?>" loading="lazy" alt="<?= $product->thumbnail()->alt_text ?>">
<?php else: ?>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-label="No image available">
                        <use href="assets/nothing.svg#root"></use>
                    </svg>
                    <p>No image available</p>
                </div>
<?php endif ?>
<?php if (!empty($product->base->variants)): ?>
                <div>
<?php foreach ($product->base->variants as $variant): ?>
                    <input type="radio" name="<?= format_product_code($product->base) ?>-variant" <?= $variant->to_radio_attributes(selected_suffix: $product->base->variants[0]->variant->code_suffix) ?>>
<?php endforeach ?>
                </div>
<?php endif ?>
                <p><?= $product->base->display_name ?></p>
                <p><?= format_price_range($product->base) ?></p>
            </div>
<?php endforeach ?>
        </main>
