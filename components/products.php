<?php
require_once __DIR__ . '/../util/db.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/ProductVariant.php';
require_once __DIR__ . '/../util/format.php';

$products = [];
$products_result = $db->query('select code_name, display_name, price_min, price_max from product_base join price_range on product = code_name where standalone = true');
while ($products_row = $products_result->fetch_assoc()) {
    $variants_result = $db->query('select code_suffix, display_name, color from product_variant join product_info on product = base and variant = code_suffix where base = \'' . $products_row['code_name'] . '\' order by ordinal asc');
    $variants = [];
    $first_thumbnail = null;
    $product_code = $products_row['code_name'];
    if ($variants_result->num_rows > 0) {
        while ($variants_row = $variants_result->fetch_assoc()) {
            $variant_code = $product_code . '_' . $variants_row['code_suffix'];
            $thumbnail_file = get_thumbnail_if_exists($variant_code);
            $variants[] = new ProductVariant($variants_row['code_suffix'], $variants_row['display_name'], $variants_row['color'], $thumbnail_file);
            if (count($variants) == 1)
                $first_thumbnail = $thumbnail_file;
            else if ($thumbnail_file)
                $prefetch[] = $thumbnail_file;
        }
    } else
        $first_thumbnail = get_thumbnail_if_exists($product_code);
    $products[] = new Product($product_code, $products_row['display_name'], $products_row['price_min'], $products_row['price_max'], $variants, $first_thumbnail);
}
?>
        <template id="no-thumbnail">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" title="No image available" aria-label="No image available">
                <use href="assets/ban-solid.svg#root"></use>
            </svg>
            <span>No image available</span>
        </template>
        <main>
            <section>
<?php foreach ($products as $product): ?>
                <a href="product?id=<?= $product->code_name ?>">
                    <article data-product="<?= $product->code_name ?>">
                        <section>
<?php if ($product->first_thumbnail): ?>
                            <img src="<?= $product->first_thumbnail ?>" loading="lazy">
<?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <use href="assets/ban-solid.svg#root"></use>
                            </svg>
                            <span>No image available</span>
<?php endif ?>
                        </section>
<?php if (!empty($product->variants)): ?>
                        <section>
<?php foreach ($product->variants as $index => $variant): ?>
                            <input type="radio" data-variant-suffix="<?= $variant->code_suffix ?>" data-color="#<?= $variant->color ?>"<?php if ($variant->thumbnail): ?> data-thumbnail="<?= $variant->thumbnail ?>"<?php endif ?> name="<?= format_product_code($product) ?>-variant" title="<?= $variant->display_name ?>"<?php if ($index == 0): ?> checked="checked"<?php endif ?>>
<?php endforeach ?>
                        </section>
<?php endif ?>
                        <section>
                            <span><?= $product->display_name ?></span>
                            <small><?= format_price_range($product) ?></small>
                        </section>
                    </article>
                </a>
<?php endforeach ?>
            </section>
        </main>
