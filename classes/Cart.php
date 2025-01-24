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
                $bundle_entries[$entry->bundle->code_name] = $entry->bundle->selected_suffix;
            }
        }
        if (!empty($product_entries)) {
            // Fetch product details
            $products_result = $database->find(
                table: 'product_base',
                joins: [
                    [
                        'type' => 'LEFT',
                        'table' => 'product_variant',
                        'on' => 'product_variant.base = code_name',
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
                        'on' => 'bundle_price.bundle = bundle.code_name',
                    ],
                    [
                        'type' => 'INNER',
                        'table' => 'product_in_bundle',
                        'on' => 'product_in_bundle.bundle = bundle.code_name'
                    ],
                    [
                        'type' => 'INNER',
                        'table' => 'product_base',
                        'on' => 'product_base.code_name = product_in_bundle.base'
                    ],
                    [
                        'type' => 'LEFT',
                        'table' => 'product_variant',
                        'on' => 'product_variant.base = product_in_bundle.base and product_variant.code_suffix = bundle_price.variant'
                    ],
                ],
                filters: ['bundle.code_name' => array_keys($bundle_entries)],
                options: [
                    'order_by' => [
                        'product_in_bundle.ordinal' => 'ASC',
                    ]
                ],
            );
            $bundle_display_names = [];
            $bundle_variant_display_names = [];
            $bundle_prices = [];
            $bundle_products = [];
            foreach ($bundles_result as $bundle_row) {
                if (!array_key_exists($bundle_row['bundle.code_name'], $bundle_display_names)) {
                    $bundle_display_names[$bundle_row['bundle.code_name']] = $bundle_row['bundle.display_name'];
                    $bundle_prices[$bundle_row['bundle.code_name']] = $bundle_row['price_with_discount'];
                    $bundle_products[$bundle_row['bundle.code_name']] = [];
                }
                if ($bundle_row['variant'] === $bundle_entries[$bundle_row['bundle.code_name']]) {
                    if (!array_key_exists($bundle_row['bundle.code_name'], $bundle_variant_display_names)) {
                        $bundle_variant_display_names[$bundle_row['bundle.code_name']] = $bundle_row['product_variant.display_name'];
                    }
                    $product = new ProductBase($bundle_row['product_base.code_name']);
                    $product->display_name = $bundle_row['product_base.display_name'];
                    $bundle_products[$bundle_row['bundle.code_name']][] = $product;
                }
            }
        }
        // Fill in details
        foreach ($this->entries as $entry) {
            if ($entry instanceof ProductEntry) {
                $product_row = $entry->product->array_get($product_details);
                $entry->product->base->display_name = $product_row['product_base.display_name'];
                $entry->product->price = $product_row['price_override'] ?? $product_row['price_base'];
                if ($entry->product->variant !== null) {
                    $entry->product->variant->display_name = $product_row['product_variant.display_name'];
                }
            } else { /* BundleEntry */
                $entry->bundle->display_name = $bundle_display_names[$entry->bundle->code_name];
                $entry->bundle->price_with_discount = $bundle_prices[$entry->bundle->code_name];
                $variant = null;
                if ($entry->bundle->selected_suffix !== null) {
                    $variant = new ProductVariant($entry->bundle->selected_suffix);
                    $variant->display_name = $bundle_variant_display_names[$entry->bundle->code_name];
                    $entry->bundle->variants = [$variant->code_suffix => new BundleVariant($variant)];
                } else {
                    $entry->bundle->variants = [];
                }
                $entry->bundle->products = [];
                foreach ($bundle_products[$entry->bundle->code_name] as $bundle_product) {
                    $entry->bundle->products[] = new Product($bundle_product, $variant);
                }
            }
        }
    }
}
?>
