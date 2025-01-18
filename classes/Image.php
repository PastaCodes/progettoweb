<?php
class Image {
    public string $file;
    public string $alt_text;

    public function __construct(string $file, string $alt_text) {
        $this->file = $file;
        $this->alt_text = $alt_text;
    }

    public static function if_exists(string $file, string $alt_text): ?Image {
        return file_exists_rel($file) ? new Image($file, $alt_text) : null;
    }
}
?>
