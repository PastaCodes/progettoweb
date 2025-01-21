<?php
require_once __DIR__ . "/../util/db.php";
require_once __DIR__ . "/ProductBase.php";
require_once __DIR__ . "/ProductVariant.php";
require_once __DIR__ . "/Image.php";

class Product {
    public ProductBase $base;
    public ?ProductVariant $variant;
    public float|false $price = false;
    private Image|null|false $thumbnail = false;

    public function __construct(ProductBase $base, ?ProductVariant $variant = null) {
        $this->base = $base;
        $this->variant = $variant;
    }

    public static function from(string $base_code_name, ?string $variant_code_suffix = null): Product {
        return new Product(new ProductBase($base_code_name), $variant_code_suffix === null ? null : new ProductVariant($variant_code_suffix));
    }

    public function full_code_name(): string {
        return $this->variant === null ? $this->base->code_name : $this->base->code_name . '_' . $this->variant->code_suffix;
    }

    public function thumbnail(): ?Image {
        if ($this->thumbnail === false) {
            $this->thumbnail = Image::if_exists('assets/thumbnails/' . $this->full_code_name() . '.png',
                'Photo of ' . ($this->variant === null ? $this->base->display_name : $this->base->display_name . ' in the ' . $this->variant->display_name . ' variant')
            );
        }
        return $this->thumbnail;
    }

    public function to_url_params(): string {
        return $this->variant === null ? 'id=' . $this->base->code_name : 'id=' . $this->base->code_name . '&variant=' . $this->variant->code_suffix;
    }

    public function to_radio_attributes(string $selected_suffix, bool $include_price = false): string {
        $html = $this->variant->to_radio_attributes($selected_suffix);
        if ($this->thumbnail() !== null) {
            $html .= ' data-thumbnail-file="' . $this->thumbnail()->file .'" data-thumbnail-alt="'. $this->thumbnail()->alt_text . '"';
        }
        if ($include_price) {
            $html .= ' data-price="' . number_format($this->price, 2) . '"';
        }
        return $html;
    }

    public function to_variants_data(): string {
        $data = [];
        foreach ($this->base->variants as $variant) {
            $variant_data = ['price' => $variant->price];
            if ($variant->thumbnail() !== null) {
                $variant_data['thumbnail_file'] = $variant->thumbnail()->file;
                $variant_data['thumbnail_alt'] = $variant->thumbnail()->alt_text;
            }
            $data[$variant->variant->code_suffix] = $variant_data;
        }
        return json_encode($data);
    }

    public static function fetch_products(?string $search = null, ?string $category = null): array {
        global $database;
        $filters = ['standalone' => true];
        if ($category) {
            $filters['category'] = $category;
        }
        if ($search) {
            $filters['product_base.display_name'] = "%$search%";
        }
        $products_result = $database->find(
            table: 'product_base',
            joins: [
                [
                    'type' => 'INNER',
                    'table' => 'price_range',
                    'on' => 'product = code_name',
                ],
                [
                    'type' => 'LEFT',
                    'table' => 'product_variant',
                    'on' => 'base = code_name',
                ]
            ],
            filters: $filters,
            options: ['order_by' => ['code_name' => 'ASC', 'ordinal' => 'ASC']]
        );
        $products = [];
        foreach ($products_result as $products_row) {
            if (!array_key_exists($products_row['code_name'], $products)) {
                $product = $products[$products_row['code_name']] = Product::from($products_row['code_name'], $products_row['code_suffix']);
                $product->base->display_name = $products_row['product_base.display_name'];
                $product->base->price_min = $products_row['price_min'];
                $product->base->price_max = $products_row['price_max'];
                $variant_product = $product;
            } else {
                $product = $products[$products_row['code_name']];
                $variant_product = new Product($product->base, new ProductVariant($products_row['code_suffix']));
            }
            if (!$products_row['code_suffix']) {
                continue;
            }
            $variant_product->variant->display_name = $products_row['product_variant.display_name'];
            $variant_product->variant->color = $products_row['color'];
            $product->base->variants[] = $variant_product;
        }
        return $products;
    }

    public function fetch_all_details() {
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
                    'on' => 'base = code_name AND (code_suffix is null or variant = code_suffix)',
                ]
            ],
            filters: ['code_name' => $this->base->code_name],
            options: ['order_by' => ['ordinal' => 'ASC']]
        );
        foreach ($details_result as $details_row) {
            if ($this->base->display_name === false) {
                $this->base->display_name = $details_row['product_base.display_name'];
                $this->base->short_description = $details_row['short_description'];
                $this->base->is_standalone = $details_row['standalone'];
            }
            if ($details_row['code_suffix'] === $this->variant?->code_suffix) {
                $variant_product = $this;
            } else {
                $variant_product = new Product($this->base, new ProductVariant($details_row['code_suffix']));
            }
            if ($variant_product !== null) {
                if ($variant_product->variant !== null) {
                    $variant_product->variant->display_name = $details_row['product_variant.display_name'];
                    $variant_product->variant->color = $details_row['color'];
                    $this->base->variants[] = $variant_product;
                }
                $variant_product->price = $details_row['price'];
            }
        }
    }

    public function array_set(array|stdClass &$array, mixed $value) {
        if ($this->variant === null) {
            $array[$this->base->code_name] = $value;
        } else {
            if (!array_key_exists($this->base->code_name, $array)) {
                $array[$this->base->code_name] = [];
            }
            $array[$this->base->code_name][$this->variant->code_suffix] = $value;
        }
    }

    public function array_get(array|stdClass $array): mixed {
        if ($this->variant === null) {
            return $array[$this->base->code_name];
        }
        return $array[$this->base->code_name][$this->variant->code_suffix];
    }

    /**
     * @param array $array inner values should not be arrays
     * @param callable $consumer should take a Product and a value
     */
    public static function array_iterate(array|stdClass $array, callable $consumer) {
        foreach ($array as $base_code_name => $entry) {
            $base = new ProductBase($base_code_name);
            if (is_array($entry) || is_object($entry)) {
                foreach ($entry as $variant_code_suffix => $value) {
                    $consumer(new Product($base, new ProductVariant($variant_code_suffix)), $value);
                }
            } else {
                $consumer(new Product($base), $entry);
            }
        }
    }
}
?>
