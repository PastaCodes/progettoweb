<?php
require_once __DIR__ . "/../util/db.php";
require_once __DIR__ . "/ProductBase.php";
require_once __DIR__ . "/ProductVariant.php";
require_once __DIR__ . "/Product.php";

class Bundle {
    public string $code_name;
    public ?string $selected_suffix;
    public string|false $display_name = false;
    public float|false $multiplier = false;
    public array|false $products = false; /* array of string => Product */
    public array|false $variants = false; /* array of string => ProductVariant */
    private float|false $price_before_discount = false;

    public function __construct(string $code_name, ?string $selected_suffix = null) {
        $this->code_name = $code_name;
        $this->selected_suffix = $selected_suffix;
    }

    public function price_before_discount(): float {
        if ($this->price_before_discount === false) {
            $this->price_before_discount = 0;
            foreach ($this->products as $product) {
                $this->price_before_discount += $product->price;
            }
        }
        return $this->price_before_discount;
    }

    public function price_with_discount(): float {
        return $this->multiplier * $this->price_before_discount();
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
                    'type'=> 'INNER',
                    'table' => 'product_info',
                    'on' => 'product_info.product = product_in_bundle.base and (product_info.variant is null or product_info.variant = bundle_variant.code_suffix)',
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
            filters: ['product_in_bundle.bundle' => $this->code_name]
        );
        $this->products = [];
        $this->variants = [];
        foreach ($details_result as $details_row) {
            if ($this->display_name === false) {
                $this->display_name = $details_row['bundle.display_name'];
                $this->multiplier = $details_row['multiplier'];
            }
            if (!array_key_exists($details_row['product_in_bundle.base'], $this->products)) {
                $product = Product::from($details_row['product_in_bundle.base']); // Variant may be set later
                $product->base->display_name = $details_row['product_base.display_name'];
                $product->base->short_description = $details_row['short_description'];
                $this->products[$details_row['product_in_bundle.base']] = $product;
            }
            $product = $this->products[$details_row['product_in_bundle.base']];
            if ($details_row['bundle_variant.code_suffix'] === null) {
                $product->price = $details_row['price'];
            } else {
                if (!array_key_exists($details_row['bundle_variant.code_suffix'], $this->variants)) {
                    $variant = new ProductVariant($details_row['bundle_variant.code_suffix']);
                    $variant->display_name = $details_row['product_variant.display_name'];
                    $variant->color = $details_row['color'];
                    $this->variants[$details_row['bundle_variant.code_suffix']] = $variant;
                }
                $variant = $this->variants[$details_row['bundle_variant.code_suffix']];
                if ($details_row['bundle_variant.code_suffix'] === $this->selected_suffix) {
                    $product->variant = $variant;
                    $variant_product = $product;
                } else {
                    $variant_product = new Product($product->base, $variant);
                }
                $variant_product->price = $details_row['price'];
                $product->base->variants[] = $variant_product;
            }
        }
    }
}
?>