<?php
class Product {
    public string $name;
    public float $price;
    public string $imageUrl;

    function __construct(string $name, float $price, string $imageUrl) {
        $this->name = $name;
        $this->price = $price;
        $this->imageUrl = $imageUrl;
    }
}
?>
