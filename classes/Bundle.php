<?php
require_once __DIR__ . "/../util/db.php";
require_once __DIR__ . "/ProductBase.php";
require_once __DIR__ . "/ProductVariant.php";
require_once __DIR__ . "/Product.php";

class Bundle {
    public string $code_name;
    public ?string $selected_suffix;
    public string|false $display_name = false;
    public array|false $products = false; /* array of string => Product */
    public array|false $variants = false; /* array of string => BundleVariant */
    public int|false $variants_count = false;
    public float|null|false $price_before_discount = false;
    public float|null|false $price_with_discount = false;

    public function __construct(string $code_name, ?string $selected_suffix = null) {
        $this->code_name = $code_name;
        $this->selected_suffix = $selected_suffix;
    }

    public function to_url_params(): string {
        $url = "id=" . $this->code_name;
        if ($this->selected_suffix !== null) {
            $url .= "&variant=". $this->selected_suffix;
        }
        return $url;
    }

    private function fill_details(array $details_row) {
        if ($this->display_name === false) {
            $this->display_name = $details_row['bundle.display_name'];
        }
        if ($details_row['bundle_variant.code_suffix'] === $this->selected_suffix) {
            $this->price_before_discount = $details_row['price_before_discount'] ?? false;
            $this->price_with_discount = $details_row['price_with_discount'] ?? false;
        }
        if (!array_key_exists($details_row['product_in_bundle.base'], $this->products)) {
            $product = Product::from($details_row['product_in_bundle.base']); // Variant may be set later
            $product->base->display_name = $details_row['product_base.display_name'];
            $product->base->short_description = $details_row['short_description'];
            $product->base->variants = [];
            $this->products[$details_row['product_in_bundle.base']] = $product;
        }
        $product = $this->products[$details_row['product_in_bundle.base']];
        if ($details_row['product_variant.code_suffix'] === null) {
            $product->price = $details_row['price'] ?? false;
        } else {
            if (!array_key_exists($details_row['bundle_variant.code_suffix'], $this->variants)) {
                $variant = new ProductVariant($details_row['bundle_variant.code_suffix']);
                $variant->display_name = $details_row['product_variant.display_name'];
                $variant->color = $details_row['color'];
                $bundle_variant = new BundleVariant($variant);
                $bundle_variant->price_before_discount = $details_row['price_before_discount'] ?? false;
                $bundle_variant->price_with_discount = $details_row['price_with_discount'] ?? false;
                $this->variants[$details_row['bundle_variant.code_suffix']] = $bundle_variant;
            }
            $variant = $this->variants[$details_row['bundle_variant.code_suffix']]->variant;
            if ($details_row['bundle_variant.code_suffix'] === $this->selected_suffix) {
                $product->variant = $variant;
                $variant_product = $product;
            } else {
                $variant_product = new Product($product->base, $variant);
            }
            $variant_product->price = $details_row['price'] ?? false;
            if (is_array($product->base->variants)) {
                $product->base->variants[] = $variant_product;
            }
        }
    }

    public function fetch_details() {
        global $database;
        $details_result = $database->find(
            table: 'product_in_bundle',
            joins: [
                [
                    'type' => 'LEFT',
                    'table' => 'bundle_variant',
                    'on' => 'bundle_variant.bundle = product_in_bundle.bundle',
                ],
                [
                    'type'=> 'LEFT',
                    'table' => 'bundle_price',
                    'on' => 'bundle_price.code_name = product_in_bundle.bundle and (bundle_price.variant is null or bundle_price.variant = bundle_variant.code_suffix)',
                ],
                [
                    'type' => 'LEFT',
                    'table' => 'product_variant',
                    'on' => 'product_variant.base = product_in_bundle.base and product_variant.code_suffix = bundle_variant.code_suffix',
                ],
                [
                    'type'=> 'INNER',
                    'table' => 'product_base',
                    'on' => 'product_in_bundle.base = product_base.code_name',
                ],
                [
                    'type'=> 'INNER',
                    'table' => 'bundle',
                    'on' => 'bundle.code_name = product_in_bundle.bundle',
                ],
            ],
            filters: ['product_in_bundle.bundle' => $this->code_name],
            options: [
                'order_by' => [
                    'product_in_bundle.ordinal' => 'ASC',
                    'product_variant.ordinal' => 'ASC'
                ]
            ]
        );
        $this->products = [];
        $this->variants = [];
        foreach ($details_result as $details_row) {
            $this->fill_details($details_row);
        }
    }

    public static function fetch_bundles(): array {
        global $database;
        $bundles_result = $database->find(
            table: 'product_in_bundle',
            custom_columns: [
                'variants_count' => 'count(bundle_variant.code_suffix)'
            ],
            joins: [
                [
                    'type' => 'LEFT',
                    'table' => 'bundle_variant',
                    'on' => 'bundle_variant.bundle = product_in_bundle.bundle',
                ],
                [
                    'type' => 'LEFT',
                    'table' => 'product_variant',
                    'on' => 'product_variant.base = product_in_bundle.base and product_variant.code_suffix = bundle_variant.code_suffix',
                ],
                [
                    'type'=> 'INNER',
                    'table' => 'product_base',
                    'on' => 'product_in_bundle.base = product_base.code_name',
                ],
                [
                    'type'=> 'INNER',
                    'table' => 'bundle',
                    'on' => 'bundle.code_name = product_in_bundle.bundle',
                ]
            ],
            options: [
                'order_by' => [
                    'bundle.code_name' => 'ASC',
                    'product_in_bundle.ordinal' => 'ASC',
                    'product_variant.ordinal' => 'ASC'
                ],
                'group_by' => 'product_in_bundle.bundle, product_in_bundle.base'
            ]
        );
        $bundles = [];
        foreach ($bundles_result as $bundles_row) {
            if (!array_key_exists($bundles_row['bundle.code_name'], $bundles)) {
                $bundles[$bundles_row['bundle.code_name']] = new Bundle($bundles_row['bundle.code_name']);
                $bundles[$bundles_row['bundle.code_name']]->variants_count = $bundles_row['variants_count'];
                $bundles[$bundles_row['bundle.code_name']]->products = [];
                $bundles[$bundles_row['bundle.code_name']]->variants = [];
            }
            if ($bundles_row['bundle_variant.code_suffix'] !== null) {
                $bundles[$bundles_row['bundle.code_name']]->selected_suffix = $bundles_row['bundle_variant.code_suffix'];
            }
            $bundles[$bundles_row['bundle.code_name']]->fill_details($bundles_row);
        }
        return $bundles;
    }
}

class BundleVariant {
    public ProductVariant $variant;
    public float|false $price_before_discount = false;
    public float|false $price_with_discount = false;

    public function __construct(ProductVariant $variant) {
        $this->variant = $variant;
    }

    public function to_radio_attributes(?string $selected_suffix): string {
        return $this->variant->to_radio_attributes($selected_suffix) .
            ' data-price-before="' . number_format($this->price_before_discount, 2) .
            '" data-price="' . number_format($this->price_with_discount, 2) . '"';
    }
}
?>
