<?php
require_once '../classes/Bundle.php';
require_once '../util/format.php';

$bundle = new Bundle($_GET['id'], $_GET['variant'] ?? null);
$bundle->fetch_details();
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
            <section>
                <h1><?= $bundle->display_name ?></h1>
                <section>
                    <h2>Included products:</h2>
<?php foreach ($bundle->products as $product): ?>
                    <div data-variants-data="<?= htmlspecialchars($product->to_variants_data(), ENT_QUOTES, 'UTF-8') ?>">
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
                        <a href="product?<?= $product->to_url_params() ?>" title="Go to product page"><?= $product->base->display_name ?> <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label=""><use href="assets/link.svg#link"></use></svg></a>
                        <p><?= $product->base->short_description ?></p>
                    </div>
<?php endforeach ?>
                </section>
<?php if (!empty($bundle->variants)): ?>
                <section>
                    <h2>Choose your style:</h2>
                    <p><?= $bundle->variants[$bundle->selected_suffix]->display_name ?></p>
                    <div>
<?php foreach ($bundle->variants as $variant): ?>
                        <input type="radio" name="variant" <?= $variant->to_radio_attributes($bundle->selected_suffix) ?>>
<?php endforeach ?>
                    </div>
                </section>
<?php endif ?>
                <p data-multiplier="<?= number_format($bundle->multiplier, 4) ?>"><del><?= format_price($bundle->price_before_discount()) ?></del> <?= format_price($bundle->price_with_discount()) ?></p>
                <button data-bundle-name="<?= $bundle->code_name ?>"<?php if ($bundle->selected_suffix !== null): ?> data-variant-suffix="<?= $bundle->selected_suffix ?>"<?php endif ?>>Add to cart</button>
            </section>
        </main>
