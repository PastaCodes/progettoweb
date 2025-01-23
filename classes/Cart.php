<?php
require_once __DIR__ . '/../util/db.php';
require_once __DIR__ . '/CartEntry.php';
require_once __DIR__ . '/Product.php';

class Cart {
    public array $entries = [];

    public static function load_from_cookie(): Cart {
        $cart = new Cart;
        $cart->entries = array_map('CartEntry::from', json_decode($_COOKIE['cart'] ?? '[]'));
        return $cart;
    }

    public function fetch_details() {
        global $database;
        $product_entries = [];
        $bundle_entries = [];
        foreach ($this->entries as $entry) {
            if ($entry instanceof ProductEntry) {
                $product_entries[] = $entry->product->base->code_name;
            } else { /* BundleEntry */
                $bundle_entries[] = $entry->bundle->code_name;
            }
        }
        if (!empty($product_entries)) {
            // Fetch product details
            $products_result = $database->find(
                table: 'product_base',
                joins: [
                    [
                        'type' => 'INNER',
                        'table' => 'product_info',
                        'on' => 'product_info.base = code_name',
                    ],
                    [
                        'type' => 'LEFT',
                        'table' => 'product_variant',
                        'on' => 'product_variant.base = code_name and (code_suffix is null or variant = code_suffix)',
                    ]
                ],
                filters: ['product_base.code_name' => $product_entries],
                options: ['distinct' => true]
            );
            $product_details = [];
            foreach ($products_result as $product_row) {
                Product::from($product_row['code_name'], $product_row['code_suffix'])->array_set($product_details, $product_row);
            }
        }
        if (!empty($bundle_entries)) {
            // Fetch bundle details TODO
            $bundles_result = $database->find(
                table: 'bundle',
                joins: [
                    [
                        'type' => 'INNER',
                        'table' => 'bundle_price',
                        'on' => 'bundle_price.code_name = bundle.code_name',
                    ],
                    
                ],
                filters: ['bundle.code_name' => $bundle_entries]
            );
            $bundle_details = [];
            foreach ($bundles_result as $bundle_row) {
                $bundle_details[$bundle_row['bundle.code_name']] = $bundle_row;
            }
        }
        // Fill in details
        foreach ($this->entries as $entry) {
            if ($entry instanceof ProductEntry) {
                $product_row = $entry->product->array_get($product_details);
                $entry->product->base->display_name = $product_row['product_base.display_name'];
                $entry->product->price = $product_row['price'];
                if ($entry->product->variant !== null) {
                    $entry->product->variant->display_name = $product_row['product_variant.display_name'];
                }
            } else { /* BundleEntry */
                $bundle_row = $bundle_details[$entry->bundle->code_name];
                $entry->bundle->display_name = $bundle_row['display_name'];
                $entry->bundle->price_with_discount = $bundle_row['price_with_discount'];
                if ($entry->bundle->variants === false) {
                    $entry->bundle->variants = [];
                    if ($entry->bundle->selected_suffix !== null) {
                        $variant = new ProductVariant($entry->bundle->selected_suffix);
                        $variant->display_name = $variant->code_suffix; // TODO
                        $entry->bundle->variants[$variant->code_suffix] = new BundleVariant($variant);
                    }
                }
            }
        }
    }
}
?>
