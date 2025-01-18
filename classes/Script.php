<?php
class Script {
    public ?string $src;
    public string $contents;
    public ?string $script_type;

    private function __construct(?string $src = null, string $contents = '', ?string $type = null) {
        $this->src = $src;
        $this->contents = $contents;
        $this->script_type = $type;
    }

    public function to_script_tag() : string  {
        return '<script' . ($this->script_type === null ? '' : ' type=' . $this->script_type) . ($this->src === null ? '>' : ' src="' . $this->src . '">') . $this->contents . '</script>' . "\n";
    }

    public static function external(string $src, ?string $type = null) : Script {
        return new Script(src: $src, type: $type);
    }

    public static function internal(string $contents, ?string $type = null) : Script {
        return new Script(contents: $contents, type: $type);
    }
}
?>
