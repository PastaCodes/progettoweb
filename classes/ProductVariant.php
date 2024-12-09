<?php
class ProductVariant {
    public string $display_name;
    public string $color;
    public ?string $thumbnail;

    function __construct(string $display_name, string $color, ?string $thumbnail = null) {
        $this->display_name = $display_name;
        $this->color = $color;
        $this->thumbnail = $thumbnail;
    }
}
?>