<?php
class Product {
    public string $name;
    public float $priceMin;
    public float $priceMax;
    public ?string $image;

    function __construct(string $name, float $priceMin, float $priceMax, ?string $image = null) {
        $this->name = $name;
        $this->priceMin = $priceMin;
        $this->priceMax = $priceMax;
        $this->image = $image;
    }
}
?>
