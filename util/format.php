<?php
function format_price(float $price) : string {
    return number_format($price, 2, decimal_separator: ",", thousands_separator: ".");
}

function format_price_range(ProductBase $product) : string {
    return '&euro; ' . (
        $product->price_max == $product->price_min
        ? format_price($product->price_min)
        : format_price($product->price_min) . ' - &euro; ' . format_price($product->price_max)
    );
}

function format_product_code(ProductBase $product) : string {
    return str_replace('_', '-', $product->code_name);
}
?>
