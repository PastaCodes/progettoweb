<?php
require_once __DIR__ . '/CartEntry.php';
require_once __DIR__ . '/Product.php';

class Cart {
    public array $entries = [];

    public static function load_from_cookie(): Cart {
        $cart = new Cart;
        $data = json_decode($_COOKIE['cart'] ?? '{}');
        Product::array_iterate($data, function($product, $quantity) use($cart) {
            $cart->entries[] = new CartEntry($product, $quantity);
        });
        return $cart;
    }

    public function fetch_details() {
        global $db;
        $details_result = $db->query('
            select b.code_name base_code_name, v.code_suffix variant_code_suffix,
                    b.display_name base_display_name, v.display_name variant_display_name, price
	            from product_base b left join product_variant v on v.base = b.code_name
                    join product_info on product = b.code_name and (v.code_suffix is null or variant = v.code_suffix)
                where ' . implode(' or ', array_map(function($entry) {
                    return '(b.code_name = \'' . $entry->product->base->code_name . '\' and ' .
                        ($entry->product->variant === null ?
                            'v.code_suffix is null' :
                            'v.code_suffix = \'' . $entry->product->variant->code_suffix . '\''
                        ) . ')';
                }, $this->entries))
        );
        $details = [];
        while ($details_row = $details_result->fetch_assoc()) {
            Product::from($details_row['base_code_name'], $details_row['variant_code_suffix'])->array_set($details, $details_row);
        }
        foreach ($this->entries as $entry) {
            $details_row = $entry->product->array_get($details);
            $entry->product->base->display_name = $details_row['base_display_name'];
            $entry->product->price = $details_row['price'];
            if ($entry->product->variant !== null) {
                $entry->product->variant->display_name = $details_row['variant_display_name'];
            }
        }
    }
}
?>
