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
                        <img src="<?= $product->image ?>"/>
<?php else: ?>
                            <?php require 'assets/ban-solid.svg' ?>
                            <p>No image available</p>
<?php endif ?>
                    </section>
                    <section>
                        <span><?= $product->name ?></span>
                        <br />
                        <small><?= format_price_range($product) ?></small>
                    </section>
                </article>
<?php endforeach ?>
            </section>
        <main>
