<?php
require_once '../classes/Category.php';
require_once '../classes/Product.php';
require_once '../classes/ProductVariant.php';
require_once '../util/format.php';

$products = Product::fetch_products($_GET['search'] ?? null, $_GET['category'] ?? null);
$categories = Category::fetch_all();
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
                <label for="search" hidden="hidden">Search for products</label>
                <input id="search" type="search" name="search" placeholder="Search"<?php if (isset($_GET['search'])): ?> value="<?= $_GET['search'] ?>"<?php endif ?>>
                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value=""<?php if (!isset($_GET['category'])): ?> selected="selected"<?php endif ?>>Any</option>
<?php foreach ($categories as $category): ?>
                    <option value="<?= $category['code_name'] ?>"<?php if ($category['code_name'] == ($_GET['category'] ?? null)): ?> selected="selected"<?php endif ?>><?= $category['display_name'] ?></option>
<?php endforeach ?>
                </select>
            </fieldset>
<?php if (empty($products)): ?>
            <p>No results</p>
<?php else: ?>
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
                        <label for="<?= format_full_code($variant) ?>" hidden="hidden">Select the <?= $variant->variant->display_name ?> variant</label>
                        <input id="<?= format_full_code($variant) ?>" type="radio" name="<?= format_product_code($product->base) ?>-variant" <?= $variant->to_radio_attributes(selected_suffix: $product->base->variants[0]->variant->code_suffix) ?>>
<?php endforeach ?>
                    </fieldset>
<?php endif ?>
                    <p><?= $product->base->display_name ?></p>
                    <p><?= format_price_range($product->base) ?></p>
                </li>
<?php endforeach ?>
            </ul>
<?php endif ?>
        </main>
