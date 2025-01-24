<?php
require_once '../classes/Bundle.php';
require_once '../util/format.php';

$bundle = new Bundle($_GET['id'], $_GET['variant'] ?? null);
$bundle->fetch_details();
?>
        <template>
            <figure>
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                    <use href="assets/nothing.svg#nothing"></use>
                </svg>
                <figcaption>No image available</figcaption>
            </figure>
        </template>
        <template>
            <fieldset role="group">
                <a href="cart">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                        <use href="assets/cart.svg#cart"></use>
                    </svg>
                    In your cart
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                        <use href="assets/link.svg#link"></use>
                    </svg>
                </a>
                <button data-bundle-name="<?= $bundle->code_name ?>"<?php if ($bundle->selected_suffix !== null): ?> data-variant-suffix="<?= $bundle->selected_suffix ?>"<?php endif ?>>
                    Add another
                </button>
            </fieldset>
        </template>
        <main>
            <section>
                <h1><?= $bundle->display_name ?></h1>
                <section>
                    <h2>Included products:</h2>
                    <ul>
<?php foreach ($bundle->products as $product): ?>
                        <li data-variants-data="<?= htmlspecialchars($product->to_variants_data(), ENT_QUOTES, 'UTF-8') ?>">
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
                            <a href="product?<?= $product->to_url_params() ?>" title="Go to product page"><?= $product->base->display_name ?> <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label=""><use href="assets/link.svg#link"></use></svg></a>
<?php if ($product->variant !== null): ?>
                            <p><?= $product->variant->display_name ?></p>
<?php endif ?>
                            <p><?= $product->base->short_description ?></p>
                        </li>
<?php endforeach ?>
                    </ul>
                </section>
<?php if (count($bundle->variants) > 1): ?>
                <section>
                    <h2>Choose your style:</h2>
                    <p><?= $bundle->variants[$bundle->selected_suffix]->variant->display_name ?></p>
                    <fieldset>
<?php foreach ($bundle->variants as $variant): ?>
                        <label for="<?= format_variant_suffix($variant) ?>" hidden="hidden">Select the <?= $variant->variant->display_name ?> variant</label>
                        <input id="<?= format_variant_suffix($variant) ?>" type="radio" name="variant" <?= $variant->to_radio_attributes($bundle->selected_suffix) ?>>
<?php endforeach ?>
                    </fieldset>
                </section>
<?php endif ?>
                <p><del><?= format_price($bundle->price_before_discount) ?></del> <ins><?= format_price($bundle->price_with_discount) ?></ins></p>
<?php if (($quantity = $bundle->quantity_in_cart()) > 0): ?>
                <fieldset role="group">
                    <a href="cart">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                            <use href="assets/cart.svg#cart"></use>
                        </svg>
                        In your cart
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                            <use href="assets/link.svg#link"></use>
                        </svg>
                    </a>
                    <button data-bundle-name="<?= $bundle->code_name ?>"<?php if ($bundle->selected_suffix !== null): ?> data-variant-suffix="<?= $bundle->selected_suffix ?>"<?php endif ?><?php if ($quantity === 99): ?> disabled="disabled"<?php endif ?>>
                        Add another
                    </button>
                </fieldset>
<?php else: ?>
                <button data-bundle-name="<?= $bundle->code_name ?>"<?php if ($bundle->selected_suffix !== null): ?> data-variant-suffix="<?= $bundle->selected_suffix ?>"<?php endif ?>>
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                        <use href="assets/cart.svg#cart"></use>
                    </svg>
                    Add to cart
                </button>
<?php endif ?>
            </section>
        </main>
