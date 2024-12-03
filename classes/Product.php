<?php
class Product {
    public string $code;
    public string $display_name;
    public float $priceMin;
    public float $priceMax;
    public ?string $image;

    function __construct(string $code, string $display_name, float $priceMin, float $priceMax, ?string $image = null) {
        $this->code = $code;
        $this->display_name = $display_name;
        $this->priceMin = $priceMin;
        $this->priceMax = $priceMax;
        $this->image = $image;
    }
}
?>
