<?php
class Product {
    public string $code_name;
    public string $display_name;
    public float $price_min;
    public float $price_max;
    public array $variants;
    public ?string $first_thumbnail;
    public ?string $short_description;

    function __construct(string $code_name, string $display_name, float $price_min, float $price_max, array $variants = [], ?string $first_thumbnail = null, ?string $short_description = null) {
        $this->code_name = $code_name;
        $this->display_name = $display_name;
        $this->price_min = $price_min;
        $this->price_max = $price_max;
        $this->variants = $variants;
        $this->first_thumbnail = $first_thumbnail;
        $this->short_description = $short_description;
    }
}
?>
