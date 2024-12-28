<?php
require __DIR__ . '/../util/db.php';
require __DIR__ . '/../classes/Product.php';
require __DIR__ . '/../classes/ProductVariant.php';
require __DIR__ . '/../util/format.php';
require __DIR__ . '/../util/files.php';

$product = null;
$product_result = $db->query('select code_name, display_name, price_min, price_max from product_base join price_range on product = code_name where standalone = true and code_name = \'chain_bracelet\'');
while ($product_row = $product_result->fetch_assoc()) {
    $variants_result = $db->query('select code_suffix, display_name, color from product_variant join product_info on product = base and variant = code_suffix where base = \'' . $product_row['code_name'] . '\' order by ordinal asc');
    $variants = [];
    $first_thumbnail = null;
    $product_code = $product_row['code_name'];
    if ($variants_result->num_rows > 0) {
        while ($variants_row = $variants_result->fetch_assoc()) {
            $variant_code = $product_code . '_' . $variants_row['code_suffix'];
            $thumbnail_file = get_thumbnail_if_exists($variant_code);
            $variants[] = new ProductVariant($variants_row['display_name'], $variants_row['color'], $thumbnail_file);
            if (count($variants) == 1)
                $first_thumbnail = $thumbnail_file;
            else if ($thumbnail_file)
                $prefetch[] = $thumbnail_file;
        }
    } else
        $first_thumbnail = get_thumbnail_if_exists($product_code);
    $product = new Product($product_code, $product_row['display_name'], $product_row['price_min'], $product_row['price_max'], $variants, $first_thumbnail);
}
?>
        <main>
        <?php if ($product === null): ?>
            <section>
                <h1>Product not found</h1>
            </section>
        <?php else: ?>
            <section>
                <img src="immagine.png"></img>
            </section>
            <section>
                <h1>Titolo</h1>
                <p>Decrizione</p>
            </section>
        <?php endif ?>
        </main>
