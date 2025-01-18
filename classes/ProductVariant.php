<?php
class ProductVariant {
    public string $code_suffix;
    public string|false $display_name = false;
    public string|false $color = false;

    public function __construct(string $code_suffix) {
        $this->code_suffix = $code_suffix;
    }
}
?>
