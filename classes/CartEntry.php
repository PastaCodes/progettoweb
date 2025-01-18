<?php
class CartEntry {
    public Product $product;
    public int $quantity;

    public function __construct(Product $product, int $quantity) {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function entry_price() : float {
        return $this->quantity * $this->product->price;
    }
}
?>
