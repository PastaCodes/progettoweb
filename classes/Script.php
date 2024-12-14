<?php
class Script {
    public string $content_type;
    public ?string $src;
    public string $contents;
    public ?string $id;

    private function __construct(string $content_type, ?string $src = null, string $contents = '', ?string $id = null) {
        $this->content_type = $content_type;
        $this->src = $src;
        $this->contents = $contents;
        $this->id = $id;
    }

    public function to_script_tag() : string  {
        return '        <script type="' . $this->content_type . '"' . ($this->id ? ' id="' . $this->id . '"' : '') . ($this->src ? ' src="' . $this->src . '">' : '>') . $this->contents . '</script>' . "\n";
    }

    public static function jsExternal(string $src) : Script {
        return new Script('text/javascript', src: $src);
    }

    public static function customInternal(string $id, string $content_type, string $contents) : Script {
        return new Script($content_type, contents: $contents, id: $id);
    }
}
?>