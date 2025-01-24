<?php
class ProductBase {
    public string $code_name;
    public string|false $display_name = false;
    public float|false $price_min = false;
    public float|false $price_max = false;
    public string|false $short_description = false;
    public ?bool $is_standalone = null;
    public string|false $category = false;
    public array|false $variants = false; /* array of Product's */

    public function __construct(string $code_name) {
        $this->code_name = $code_name;
    }
}
?>
