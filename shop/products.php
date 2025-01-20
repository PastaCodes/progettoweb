<?php
require_once '../util/db.php';
require_once '../classes/Product.php';
require_once '../classes/ProductVariant.php';
require_once '../util/format.php';

$products = Product::fetch_products();
$categories = $database->find(table: 'category');
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
            <form action="shop" method="GET">
                <fieldset>
                    <label>
                        Search
                        <input type="search" name="search" placeholder="Search">
                    </label>
                    <label>
                        Category
                        <select name="category">
                            <option value=""></option>
<?php foreach ($categories as $category): ?>
                            <option value="<?= $category['display_name'] ?>"><?= $category['display_name'] ?></option>
<?php endforeach ?>
                        </select>
                    </label>
                </fieldset>
                <input type="submit" value="Filter">
            </form>
            <div>
<?php foreach ($products as $product): ?>
                <div data-product="<?= $product->base->code_name ?>" data-link="product?<?= $product->to_url_params() ?>" tabindex="0">
<?php if ($product->thumbnail() !== null): ?>
                    <img src="<?= $product->thumbnail()->file ?>" loading="lazy" alt="<?= $product->thumbnail()->alt_text ?>">
<?php else: ?>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                            <use href="assets/nothing.svg#nothing"></use>
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
            </div>
        </main>
