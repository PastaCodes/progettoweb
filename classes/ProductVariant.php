<?php
class ProductVariant {
    public string $code_suffix;
    public string $display_name;
    public string $color;
    public ?string $thumbnail;

    function __construct(string $code_suffix, string $display_name, string $color, ?string $thumbnail = null) {
        $this->code_suffix = $code_suffix;
        $this->display_name = $display_name;
        $this->color = $color;
        $this->thumbnail = $thumbnail;
    }
}
?>