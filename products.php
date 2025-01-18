<?php
require_once 'util/db.php';
require_once 'classes/Product.php';
require_once 'classes/ProductVariant.php';
require_once 'util/format.php';

$products = Product::fetch_products();
?>
        <template id="no-thumbnail">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-label="No image available">
                <use href="assets/nothing.svg#root"></use>
            </svg>
            <span>No image available</span>
        </template>
        <main>
            <section>
<?php foreach ($products as $product): ?>
                <a href="product?id=<?= $product->base->code_name ?>">
                    <article data-product="<?= $product->base->code_name ?>">
                        <section>
<?php if ($product->thumbnail() !== null): ?>
                            <img src="<?= $product->thumbnail()->file ?>" loading="lazy" alt="<?= $product->thumbnail()->alt_text ?>">
<?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-label="No image available">
                                <use href="assets/nothing.svg#root"></use>
                            </svg>
                            <span>No image available</span>
<?php endif ?>
                        </section>
<?php if (!empty($product->base->variants)): ?>
                        <section>
<?php foreach ($product->base->variants as $variant): ?>
                            <input type="radio" name="<?= format_product_code($product->base) ?>-variant" <?= $variant->to_radio_attributes(selected_suffix: $product->base->variants[0]->variant->code_suffix) ?>>
<?php endforeach ?>
                        </section>
<?php endif ?>
                        <section>
                            <span><?= $product->base->display_name ?></span>
                            <small><?= format_price_range($product->base) ?></small>
                        </section>
                    </article>
                </a>
<?php endforeach ?>
            </section>
        </main>
