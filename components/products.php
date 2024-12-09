<?php
require 'util/db.php';
require 'classes/Product.php';
require 'classes/ProductVariant.php';
require 'util/format.php';

$products = [];
$products_result = $db->query('select code_name, display_name, price_min, price_max from product_base join price_range on product = code_name where standalone = true');
while ($products_row = $products_result->fetch_assoc()) {
    $variants_result = $db->query('select display_name, color, thumbnail from product_variant join product_info on product = base and variant = code_suffix where base = \'' . $products_row['code_name'] . '\' order by ordinal asc');
    $variants = [];
    $first_thumbnail = null;
    if ($variants_result->num_rows > 0) {
        while ($variants_row = $variants_result->fetch_assoc()) {
            if ($variants == [])
                $first_thumbnail = $variants_row['thumbnail'];
            $variants[] = new ProductVariant($variants_row['display_name'], $variants_row['color'], $variants_row['thumbnail']);
        }
    } else {
        $info_result = $db->query('select thumbnail from product_info where product = \'' . $products_row['code_name'] . '\'');
        if ($info_row = $variants_result->fetch_assoc())
            $first_thumbnail = $info_row['thumbnail'];
    }
    $products[] = new Product($products_row['code_name'], $products_row['display_name'], $products_row['price_min'], $products_row['price_max'], $variants, $first_thumbnail);
}
?>
        <template id="no-thumbnail">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <use href="assets/ban-solid.svg#root"></use>
            </svg>
            <span>No image available</span>
        </template>
        <main>
            <section>
<?php foreach ($products as $product): ?>
                <article>
                    <section>
<?php if ($product->first_thumbnail): ?>
                        <img src="<?= $product->first_thumbnail ?>" />
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
                            <input type="radio" data-color="#<?= $variant->color ?>"<?php if ($variant->thumbnail): ?> data-thumbnail="<?= $variant->thumbnail ?>"<?php endif ?> name="<?= format_product_code($product) ?>-color" title="<?= $variant->display_name ?>"<?php if ($index == 0): ?> checked="checked"<?php endif ?> />
<?php endforeach ?>
                    </section>
<?php endif ?>
                    <section>
                        <span><?= $product->display_name ?></span>
                        <small><?= format_price_range($product) ?></small>
                    </section>
                </article>
<?php endforeach ?>
            </section>
        </main>
