<?php
require_once __DIR__ . '/Product.php';
require_once __DIR__ . '/Bundle.php';

abstract class CartEntry {
    public int $quantity;

    public function __construct(int $quantity) {
        $this->quantity = $quantity;
    }

    abstract public function display_name(): string;

    abstract public function variant_display_name(): ?string;

    abstract public function unit_price() : float;

    public function entry_price() : float {
        return $this->quantity * $this->unit_price();
    }

    abstract public function to_link_attributes() : string;

    public static function from(stdClass $data): CartEntry {
        switch ($data->type) {
            case 'product':
                return new ProductEntry(Product::from($data->base, $data->variant ?? null), $data->quantity ?? 1);
            case 'bundle':
                return new BundleEntry(new Bundle($data->name, $data->variant ?? null), $data->quantity ?? 1);
        }
        die;
    }
}

class ProductEntry extends CartEntry {
    public Product $product;

    public function __construct(Product $product, int $quantity) {
        parent::__construct($quantity);
        $this->product = $product;
    }

    public function display_name(): string {
        return $this->product->base->display_name;
    }

    public function variant_display_name(): ?string {
        return $this->product->variant?->display_name;
    }

    public function unit_price() : float {
        return $this->product->price;
    }

    public function to_link_attributes() : string {
        return 'href="product?' . $this->product->to_url_params() . '" title="Go to product page"';
    }
}

class BundleEntry extends CartEntry {
    public Bundle $bundle;

    public function __construct(Bundle $bundle, int $quantity) {
        parent::__construct($quantity);
        $this->bundle = $bundle;
    }

    public function display_name(): string {
        return $this->bundle->display_name;
    }

    public function variant_display_name(): ?string {
        if ($this->bundle->selected_suffix === null) {
            return null;
        }
        return $this->bundle->variants[$this->bundle->selected_suffix]->variant->display_name;
    }

    public function unit_price() : float {
        return $this->bundle->price_with_discount;
    }

    public function to_link_attributes() : string {
        return 'href="bundle?' . $this->bundle->to_url_params() . '" title="Go to bundle page"';
    }
}
?>
