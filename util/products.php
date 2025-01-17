<?php
function compound_key(string $product_base, ?string $product_variant, array $array) : mixed {
    return $product_variant === null ? $array[$product_base] : $array[$product_base][$product_variant];
}

function to_url_params(string $product_base, ?string $product_variant) : string {
    return $product_variant === null ? 'id=' . $product_base : 'id=' . $product_base . '&variant=' . $product_variant;
}
?>
