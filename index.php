<?php
require 'util/setup.php';
require 'classes/Script.php';
$page = new Page('Products', 'components/products.php', allow_indexing: true);
$scripts[] = Script::jsExternal('script.js');
require 'components/base.php';
?>
