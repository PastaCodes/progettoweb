<?php
require_once '../util/db.php';
require_once '../util/products.php';
require_once '../util/format.php';
require_once '../classes/CartEntry.php';
$cart = [];
$product_bases = [];
$all_bases = [];
$product_variants = [];
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
$product_bases_details = [];
$product_variants_details = [];
$product_prices = [];
if (!empty($all_bases)) {
    $products_result = $db->query('select code_name, display_name from product_base where code_name in (' . implode(', ', array_map(fn($p) => '\'' . $p . '\'', $all_bases)) . ')');
    while ($products_row = $products_result->fetch_assoc()) {
        $product_bases_details[$products_row['code_name']] = $products_row;
    }
    if (!empty($product_bases)) {
        $products_result = $db->query('select product, price from product_info where product in (' . implode(', ', array_map(fn($p) => '\'' . $p . '\'', $product_bases)) . ') and variant is null');
        while ($products_row = $products_result->fetch_assoc()) {
            $product_prices[$products_row['product']] = $products_row['price'];
        }
    }
    if (!empty($product_variants)) {
        $variants_result = $db->query('select base, code_suffix, display_name, price from product_variant join product_info on product = base and variant = code_suffix where ' . implode(' or ', array_map(fn($v) => '(base =\'' . $v[0] . '\' and code_suffix = \'' . $v[1] . '\')', $product_variants)));
        while ($variants_row = $variants_result->fetch_assoc()) {
            if (!array_key_exists($variants_row['base'], $product_variants_details)) {
                $product_variants_details[$variants_row['base']] = [];
            }
            $product_variants_details[$variants_row['base']][$variants_row['code_suffix']] = $variants_row;
            if (!array_key_exists($variants_row['base'], $product_prices)) {
                $product_prices[$variants_row['base']] = [];
            }
            $product_prices[$variants_row['base']][$variants_row['code_suffix']] = $variants_row['price'];
        }
    }
}
foreach ($cart as $entry) {
    $entry->product_display_name = $product_bases_details[$entry->product_code_name]['display_name'];
    if ($entry->variant_code_suffix !== null) {
        $entry->variant_display_name = $product_variants_details[$entry->product_code_name][$entry->variant_code_suffix]['display_name'];
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
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
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
