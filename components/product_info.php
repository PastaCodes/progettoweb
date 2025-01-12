<?php
require_once __DIR__ . '/../util/db.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/ProductVariant.php';
require_once __DIR__ . '/../util/format.php';

function getProduct(string $code_name, ?string $selected_suffix) : ?Product {
    global $database;
    // Get the current item from the db
    $product_row = $database->find_one(
        table: 'product_base', 
        joins: [
            ["type" => "INNER", "table" => "price_range", "on" => "product = code_name" ]
        ],
        filters: ['code_name' => $code_name, 'standalone' => 1]
    );
    if (!$product_row) {
        return null;
    }
    $variants_result = $database->find(
        table: 'product_variant', 
        filters: ['base' => $code_name],
        options: ['order_by' => ['ordinal' => 'ASC']]
    );
    // Load the variants
    $variants = [];
    $first_thumbnail = null;
    $product_code = $product_row['code_name'];
    foreach ($variants_result as $variants_row) {
        $variant_suffix = $variants_row['code_suffix'];
        $variant_code = $product_code . '_' . $variant_suffix;
        $thumbnail_file = get_thumbnail_if_exists($variant_code);
        $variants[] = new ProductVariant($variant_suffix, $variants_row['display_name'], $variants_row['color'], $thumbnail_file);
        if (
            ($selected_suffix === null && count($variants) == 1) ||
            $variant_suffix === $selected_suffix
        )
            $first_thumbnail = $thumbnail_file;
        else if ($thumbnail_file)
            $prefetch[] = $thumbnail_file;
    }
    if (!$first_thumbnail)
        $first_thumbnail = get_thumbnail_if_exists($product_code);
    return new Product($product_code, $product_row['display_name'], $product_row['price_min'], $product_row['price_max'], $variants, $first_thumbnail, $product_row['short_description']);
}

$product = null;
$selected_suffix = null;
if (isset($_GET['id'])) {
    $code_name = $_GET['id'];
    $selected_suffix = $_GET['variant'] ?? null;
    $product = getProduct($code_name, $selected_suffix);
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
<?php if (!empty($product->variants)): ?>
            <section>
<?php foreach ($product->variants as $index => $variant): ?>
                <input type="radio" data-variant-suffix="<?= $variant->code_suffix ?>" data-color="#<?= $variant->color ?>"<?php if ($variant->thumbnail): ?> data-thumbnail="<?= $variant->thumbnail ?>"<?php endif ?> name="variant" title="<?= $variant->display_name ?>"<?php if (($selected_suffix === null && $index == 0) || $variant->code_suffix === $selected_suffix): ?> checked="checked"<?php endif ?>>
<?php endforeach ?>
            </section>
<?php endif ?>
            <section>
                <h1><?= $product->display_name ?></h1>
                <p><?= $product->short_description ?></p>
            </section>
            <section>
                <button>Add to cart</button>
            </section>
<?php endif ?>
        </main>
