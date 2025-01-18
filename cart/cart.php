<?php
require_once '../util/db.php';
require_once '../util/products.php';
require_once '../util/format.php';
require_once '../classes/CartEntry.php';
$cart = [];
$product_bases = [];
$all_bases = [];
$product_variants = [];
// Get products from cookie
$cart_cookie = $_COOKIE['shopping_cart'] ?? '{}';
foreach (json_decode($cart_cookie) as $product => $entry) {
    if (is_int($entry)) {
        $product_bases[] = $product;
        $cart[] = new CartEntry($product, null, quantity: $entry);
    } else {
        foreach ($entry as $variant => $quantity) {
            $cart[] = new CartEntry($product, $variant, $quantity);
            $product_variants[] = [$product, $variant];
        }
    }
    $all_bases[] = $product;
}
// Fetch product base and variant details
$product_details = [];
if (!empty($all_bases)) {
    $product_details = $database->find(
        'product_base',
        [
            [
                'type' => 'INNER',
                'table' => 'product_info',
                'on' => 'product = code_name',
            ],
            [
                'type' => 'LEFT',
                'table' => 'product_variant',
                'on' => 'base = code_name',
            ]
        ],
        ['product_base.code_name' => $all_bases],
        ['distinct' => true]
    );
    // Organize product details based on base and variant
    foreach ($product_details as $detail) {
        $product_bases_details[$detail['code_name']] = $detail;
        if (!$detail['variant'])
            $product_prices[$detail['product']] = $detail['price'];
        else {
            $product_variants_details[$detail['base']][$detail['code_suffix']] = $detail;
            $product_prices[$detail['base']][$detail['code_suffix']] = $detail['price'];
        }
    }
}
foreach ($cart as $entry) {
    $entry->product_display_name = $product_bases_details[$entry->product_code_name]['product_base.display_name'] ?? null;
    if ($entry->variant_code_suffix !== null) {
        $entry->variant_display_name = $product_variants_details[$entry->product_code_name][$entry->variant_code_suffix]['product_variant.display_name'] ?? null;
    }
    $entry->unit_price = compound_key($entry->product_code_name, $entry->variant_code_suffix, $product_prices);
}
?>
        <main>
            <section>
                <h1>Your cart</h1>
<?php foreach ($cart as $entry): ?>
                <article>
<?php if ($thumbnail = get_thumbnail_if_exists($entry->full_code_name())): ?>
                    <img src="<?= $thumbnail ?>" loading="lazy" alt="<?= $entry->thumbnail_alt() ?>">
<?php else: ?>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-label="No image available">
                            <use href="assets/ban-solid.svg#root"></use>
                        </svg>
                        <p>No image available</p>
                    </div>
<?php endif ?>
                    <p><a href="product?<?= to_url_params($entry->product_code_name, $entry->variant_code_suffix) ?>"><?= $entry->product_display_name ?> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><use href="assets/arrow.svg#root"></use></svg></a></p>
<?php if ($entry->variant_code_suffix !== null): ?>
                    <p><?= $entry->variant_display_name ?></p>
<?php endif ?>
                    <button>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <use href="assets/trash-can.svg#root"></use>
                        </svg>
                    </button>
                    <div role="group">
                        <button>-</button>
                        <input name="quantity" type="number" min="1" max="99" value="<?= $entry->quantity ?>">
                        <button>+</button>
                    </div>
                    <p>&euro; <?= format_price($entry->entry_price()) ?></p>
                    <p>&euro; <?= format_price($entry->unit_price) ?>/pc</p>
                </article>
<?php endforeach ?>
            </section>
        </main>
