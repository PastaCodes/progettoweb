<?php
class Product {
    public string $name;
    public float $price;
    public ?string $image;

    function __construct(string $name, float $price, ?string $image = null) {
        $this->name = $name;
        $this->price = $price;
        $this->image = $image;
    }
}
?>
