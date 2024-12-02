<?php
    require 'util/db.php';
    require 'classes/Product.php';

    $products = [];
    $result = $db->query('SELECT * FROM product JOIN price_range ON product = code WHERE NOT bundle_only');
    while ($row = $result->fetch_assoc()) {
        $products[] = new Product($row['display_name'], $row['price_min'], $row['price_max']);
    }

    function format_price(float $price) : string {
        return number_format($price, 2, decimal_separator: ",", thousands_separator: ".");
    }

    function format_price_range(Product $product) : string {
        return '&euro; ' . (
            $product->priceMax == $product->priceMin
            ? format_price($product->priceMin)
            : format_price($product->priceMin) . ' - &euro; ' . format_price($product->priceMax)
        );
    }
?>
        <main>
            <section>
<?php foreach ($products as $product): ?>
                <article>
                    <section>
<?php if ($product->image): ?>
                        <img src="<?= $product->image ?>" />
<?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <use href="assets/ban-solid.svg#root"></use>
                        </svg>
                        <span>No image available</span>
<?php endif ?>
                    </section>
                    <section>
                        <span><?= $product->name ?></span>
                        <small><?= format_price_range($product) ?></small>
                    </section>
                </article>
<?php endforeach ?>
            </section>
        <main>
