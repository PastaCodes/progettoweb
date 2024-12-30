<?php
require __DIR__ . '/../util/db.php';
require __DIR__ . '/../classes/Product.php';
require __DIR__ . '/../classes/ProductVariant.php';
require __DIR__ . '/../util/format.php';

function getProduct(string $code_name) : ?Product {
    // Get the current DB item
    $queryProductSearch = new DatabaseObject("product_base");
    $queryProductSearch->properties["standalone"] = "1";
    $queryProductSearch->properties["code_name"] = $code_name;
    $queryProductSearch->join_conditions["price_range"] = ["code_name" => "product"];
    $product_row = dbFindOne([$queryProductSearch]);
    if (!$product_row) {
        return null;
    }
    // Get the variants of said item
    $queryVariantSearch = new DatabaseObject("product_variant");
    // FIXME: to add to db stuff $queryVariantSearch->join_conditions["product_info"] = ["product" => "base", "variant" => "code_suffix"];
    $queryVariantSearch->join_conditions["product_info"] = ["base" => "product"];
    $queryVariantSearch->properties["base"] = $code_name;
    $variants_result = dbFind([$queryVariantSearch]);
    // Load the variants
    $variants = [];
    $first_thumbnail = null;
    $product_code = $product_row['code_name'];
    foreach ($variants_result as $variants_row) {
        $variant_code = $product_code . '_' . $variants_row['code_suffix'];
        $thumbnail_file = get_thumbnail_if_exists($variant_code);
        $variants[] = new ProductVariant($variants_row['display_name'], $variants_row['color'], $thumbnail_file);
        if (count($variants) == 1)
            $first_thumbnail = $thumbnail_file;
        else if ($thumbnail_file)
            $prefetch[] = $thumbnail_file;
    }
    if (!$first_thumbnail)
        $first_thumbnail = get_thumbnail_if_exists($product_code);
    return new Product($product_code, $product_row['display_name'], $product_row['price_min'], $product_row['price_max'], $variants, $first_thumbnail, $product_row['short_description']);
}

$product = null;
if (isset($_GET['code_name'])) {
    $code_name = $_GET['code_name'];
    $product = getProduct($code_name);
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
