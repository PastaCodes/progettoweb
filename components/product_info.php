<?php
require_once __DIR__ . '/../util/db.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/ProductVariant.php';
require_once __DIR__ . '/../util/format.php';

function getProduct(string $code_name, ?string $selected_suffix) : ?Product {
    global $db;
    $sql_statement = $db->prepare('select code_name, display_name, short_description, price_min, price_max from product_base join price_range on product = code_name where standalone = true and code_name = ?');
    $sql_statement->bind_param('s', $code_name);
    $sql_statement->execute();
    $product_result = $sql_statement->get_result();
    if ($product_result->num_rows <= 0) {
        return null;
    }
    $product_row = $product_result->fetch_assoc();
    $variants_result = $db->query('select code_suffix, display_name, color from product_variant join product_info on product = base and variant = code_suffix where base = \'' . $product_row['code_name'] . '\' order by ordinal asc');
    $variants = [];
    $first_thumbnail = null;
    $product_code = $product_row['code_name'];
    if ($variants_result->num_rows > 0) {
        while ($variants_row = $variants_result->fetch_assoc()) {
            $variant_suffix = $variants_row['code_suffix'];
            $variant_code = $product_code . '_' . $variant_suffix;
            $thumbnail_file = get_thumbnail_if_exists($variant_code);
            $variants[] = new ProductVariant($variants_row['code_suffix'], $variants_row['display_name'], $variants_row['color'], $thumbnail_file);
            if (
                ($selected_suffix === null && count($variants) == 1) ||
                $variant_suffix === $selected_suffix
            )
                $first_thumbnail = $thumbnail_file;
            else if ($thumbnail_file)
                $prefetch[] = $thumbnail_file;
        }
    } else
        $first_thumbnail = get_thumbnail_if_exists($product_code);
    return new Product($product_code, $product_row['display_name'], $product_row['price_min'], $product_row['price_max'], $variants, $first_thumbnail, $product_row['short_description']);
}

$product = null;
if (isset($_GET['id'])) {
    $code_name = $_GET['id'];
    $variant_suffix = $_GET['variant'] ?? null;
    $product = getProduct($code_name, $variant_suffix);
}

?>
        <template id="no-thumbnail">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <use href="assets/ban-solid.svg#root"></use>
            </svg>
            <span>No image available</span>
        </template>
        <main>
        <?php if ($product === null): ?>
            <section>
                <h1>Product not found</h1>
            </section>
        <?php else: ?>
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
            <section>
                <h1><?= $product->display_name ?></h1>
                <p><?= $product->short_description ?></p>
            </section>
        <?php endif ?>
        </main>
