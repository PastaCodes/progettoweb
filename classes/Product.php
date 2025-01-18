<?php
require_once __DIR__ ."/ProductBase.php";
require_once __DIR__ ."/ProductVariant.php";
require_once __DIR__ ."/Image.php";

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
                'Image of ' . ($this->variant === null ? $this->base->display_name : $this->base->display_name . ' in the ' . $this->variant->display_name . ' variant')
            );
        }
        return $this->thumbnail;
    }

    public function to_url_params(): string {
        return $this->variant === null ? 'id=' . $this->base->code_name : 'id=' . $this->base->code_name . '&variant=' . $this->variant->code_suffix;
    }

    public function to_radio_attributes(string $selected_suffix): string {
        $html = 'title="'. $this->variant->display_name . '" data-variant-suffix="' . $this->variant->code_suffix .'" data-color="#'. $this->variant->color . '"';
        if ($this->thumbnail() !== null) {
            $html .= ' data-thumbnail-file="' . $this->thumbnail()->file .'" data-thumbnail-alt="'. $this->thumbnail()->alt_text . '"';
        }
        if ($this->variant->code_suffix === $selected_suffix) {
            $html .= ' checked="checked"';
        }
        return $html;
    }

    public static function fetch_products(): array {
        global $db;
        $products_result = $db->query('
            select b.code_name base_code_name, b.display_name base_display_name, price_min, price_max,
                    v.code_suffix variant_code_suffix, v.display_name variant_display_name, v.color
                from product_base b left join product_variant v on v.base = b.code_name
                    join price_range on product = code_name where standalone = true
                order by v.ordinal
        ');
        $products = [];
        while ($products_row = $products_result->fetch_assoc()) {
            if (!array_key_exists($products_row['base_code_name'], $products)) {
                $product = $products[$products_row['base_code_name']] = Product::from($products_row['base_code_name'], $products_row['variant_code_suffix']);
                $product->base->display_name = $products_row['base_display_name'];
                $product->base->price_min = $products_row['price_min'];
                $product->base->price_max = $products_row['price_max'];
                $variant_product = $product;
            } else {
                $product = $products[$products_row['base_code_name']];
                $variant_product = new Product($product->base, new ProductVariant($products_row['variant_code_suffix']));
            }
            if ($products_row['variant_code_suffix'] === null) {
                continue;
            }
            $variant_product->variant->display_name = $products_row['variant_display_name'];
            $variant_product->variant->color = $products_row['color'];
            $product->base->variants[] = $variant_product;
        }
        return $products;
    }

    public function fetch_all_details() {
        global $db;
        $details_result = $db->query('
            select b.display_name base_display_name, b.short_description, b.standalone,
                    v.code_suffix variant_code_suffix, v.display_name variant_display_name, v.color, price
                from product_base b left join product_variant v on v.base = b.code_name
                    join product_info on product = b.code_name and (v.code_suffix is null or variant = v.code_suffix)
                where code_name = \'' . $this->base->code_name . '\'
                order by v.ordinal'
        );
        while ($details_row = $details_result->fetch_assoc()) {
            if ($this->base->display_name === false) {
                $this->base->display_name = $details_row['base_display_name'];
                $this->base->short_description = $details_row['short_description'];
                $this->base->is_standalone = $details_row['standalone'];
            }
            if ($details_row['variant_code_suffix'] === $this->variant?->code_suffix) {
                $variant_product = $this;
            } else {
                $variant_product = new Product($this->base, new ProductVariant($details_row['variant_code_suffix']));
            }
            if ($variant_product !== null) {
                if ($variant_product->variant !== null) {
                    $variant_product->variant->display_name = $details_row['variant_display_name'];
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
