<?php
require_once '../classes/Product.php';
require_once '../classes/ProductVariant.php';
require_once '../util/format.php';

$products = Product::fetch_products($_GET['search'] ?? null, $_GET['category'] ?? null);
$categories = $database->find(table: 'category');
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
            <fieldset>
                <input type="search" name="search" placeholder="Search"<?php if (isset($_GET['search'])): ?> value="<?= $_GET['search'] ?>"<?php endif ?>>
                <label>
                    Category
                    <select name="category">
                        <option value=""<?php if (!isset($_GET['category'])): ?> selected="selected"<?php endif ?>>Any</option>
<?php foreach ($categories as $category): ?>
                        <option value="<?= $category['display_name'] ?>"<?php if ($category['display_name'] == ($_GET['category'] ?? null)): ?> selected="selected"<?php endif ?>><?= $category['display_name'] ?></option>
<?php endforeach ?>
                    </select>
                </label>
            </fieldset>
            <ul>
<?php foreach ($products as $product): ?>
                <li data-product="<?= $product->base->code_name ?>" data-link="product?<?= $product->to_url_params() ?>" tabindex="0">
<?php if ($product->thumbnail() !== null): ?>
                    <img src="<?= $product->thumbnail()->file ?>" loading="lazy" alt="<?= $product->thumbnail()->alt_text ?>">
<?php else: ?>
                    <figure>
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                            <use href="assets/nothing.svg#nothing"></use>
                        </svg>
                        <figcaption>No image available</figcaption>
                    </figure>
<?php endif ?>
<?php if (!empty($product->base->variants)): ?>
                    <fieldset>
<?php foreach ($product->base->variants as $variant): ?>
                        <input type="radio" name="<?= format_product_code($product->base) ?>-variant" <?= $variant->to_radio_attributes(selected_suffix: $product->base->variants[0]->variant->code_suffix) ?>>
<?php endforeach ?>
                    </fieldset>
<?php endif ?>
                    <p><?= $product->base->display_name ?></p>
                    <p><?= format_price_range($product->base) ?></p>
                </li>
<?php endforeach ?>
            </ul>
        </main>
