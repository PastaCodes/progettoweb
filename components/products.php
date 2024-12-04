<?php
    require 'util/db.php';
    require 'classes/Product.php';

    $products = [];
    $result = $db->query('SELECT * FROM product JOIN price_range ON product = code WHERE NOT bundle_only');
    while ($row = $result->fetch_assoc()) {
        $products[] = new Product($row['code'], $row['display_name'], $row['price_min'], $row['price_max']);
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
        <script>
        // For silly little browsers that do not support attr styling
        window.onload = () => {
            document.querySelectorAll('input[data-color]').forEach(input => {
                input.style.setProperty('--radio-color', input.getAttribute('data-color'));
            });
        }
        </script>
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
                        <fieldset>
<?php
    $colors = [];
    $result = $db->query('SELECT `color` FROM `product_variant` WHERE `product` = \'' . $product->code . '\'');
    while ($row = $result->fetch_assoc()) {
        $colors[] = $row['color'];
    }
    $checked = true;
?>
<?php foreach ($colors as $color): ?>
                            <input type="radio" data-color="#<?= $color ?>" name="<?= $product->code ?>_color"<?= $checked ? ' checked="checked"' : '' ?> />
<?php $checked = false; ?>
<?php endforeach ?>
                        </fieldset>
                    </section>
                    <section>
                        <span><?= $product->display_name ?></span>
                        <small><?= format_price_range($product) ?></small>
                    </section>
                </article>
<?php endforeach ?>
            </section>
        </main>
