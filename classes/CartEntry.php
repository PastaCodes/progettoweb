<?php
class CartEntry {
    public string $product_code_name;
    public ?string $variant_code_suffix;
    public ?string $product_display_name = null;
    public ?string $variant_display_name = null;
    public int $quantity;
    public ?float $unit_price = null;

    function __construct(string $product_code_name, ?string $variant_code_suffix = null, int $quantity) {
        $this->product_code_name = $product_code_name;
        $this->variant_code_suffix = $variant_code_suffix;
        $this->quantity = $quantity;
    }

    function full_code_name() : string {
        if ($this->variant_code_suffix) {
            return $this->product_code_name . '_' . $this->variant_code_suffix;
        }
        return $this->product_code_name;
    }

    function entry_price() : float {
        return $this->quantity * $this->unit_price;
    }

    function thumbnail_alt() : string {
        return 'Image of ' . ($this->variant_code_suffix === null ? $this->product_display_name : $this->product_display_name . ' in the ' . $this->variant_display_name . ' variant');
    }
}
?>
