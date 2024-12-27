<?php
class Script {
    public ?string $src;
    public string $contents;

    private function __construct(?string $src = null, string $contents = '') {
        $this->src = $src;
        $this->contents = $contents;
    }

    public function to_script_tag() : string  {
        return '<script type="text/javascript"' . ($this->src ? ' src="' . $this->src . '">' : '>') . $this->contents . '</script>' . "\n";
    }

    public static function external(string $src) : Script {
        return new Script(src: $src);
    }

    public static function internal(string $contents) : Script {
        return new Script(contents: $contents);
    }
}
?>