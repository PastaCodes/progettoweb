<?php
class ProductVariant {
    public string $code_suffix;
    public string|false $display_name = false;
    public string|false $color = false;

    public function __construct(string $code_suffix) {
        $this->code_suffix = $code_suffix;
    }

    public function to_radio_attributes(string $selected_suffix): string {
        $html = 'title="'. $this->display_name . '" data-variant-suffix="' . $this->code_suffix .'" data-color="#'. $this->color . '"';
        if ($this->code_suffix === $selected_suffix) {
            $html .= ' checked="checked"';
        }
        return $html;
    }
}
?>
