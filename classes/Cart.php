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
        global $database;
        $details_result = $database->find(
            table: 'product_base',
            joins: [
                [
                    'type' => 'INNER',
                    'table' => 'product_info',
                    'on' => 'product = code_name',
                ],
                [
                    'type' => 'LEFT',
                    'table' => 'product_variant',
                    'on' => 'base = code_name and (code_suffix is null or variant = code_suffix)',
                ]
            ],
            filters: ['product_base.code_name' => array_map(fn($entry): string => $entry->product->base->code_name, $this->entries)],
            options: ['distinct' => true]
        );
        $details = [];
        foreach ($details_result as $details_row) {
            Product::from($details_row['code_name'], $details_row['code_suffix'])->array_set($details, $details_row);
        }
        foreach ($this->entries as $entry) {
            $details_row = $entry->product->array_get($details);
            $entry->product->base->display_name = $details_row['product_base.display_name'];
            $entry->product->price = $details_row['price'];
            if ($entry->product->variant !== null) {
                $entry->product->variant->display_name = $details_row['product_variant.display_name'];
            }
        }
    }
}
?>
