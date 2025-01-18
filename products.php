<?php
require_once 'util/db.php';
require_once 'classes/Product.php';
require_once 'classes/ProductVariant.php';
require_once 'util/format.php';

$products = [];
$products_result = $db->query('
    select b.code_name base_code_name, b.display_name base_display_name, price_min, price_max,
            v.code_suffix variant_code_suffix, v.display_name variant_display_name, v.color
        from product_base b left join product_variant v on v.base = b.code_name
            join price_range on product = code_name where standalone = true
        order by v.ordinal
');
while ($products_row = $products_result->fetch_assoc()) {
    if (!array_key_exists($products_row['base_code_name'], $products)) {
        $product = $products[$products_row['base_code_name']] = Product::from($products_row['base_code_name'], $products_row['variant_code_suffix']);
        $product->base->display_name = $products_row['base_display_name'];
        $product->base->price_min = $products_row['price_min'];
        $product->base->price_max = $products_row['price_max'];
        $variant_product = $product;
    } else {
        $product = $products[$products_row['base_code_name']];
        $variant_product = new Product($product->base, new ProductVariant($products_row['variant_code_suffix']));
    }
    if ($products_row['variant_code_suffix'] === null) {
        continue;
    }
    $variant_product->variant->display_name = $products_row['variant_display_name'];
    $variant_product->variant->color = $products_row['color'];
    $product->base->variants[] = $variant_product;
}
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
