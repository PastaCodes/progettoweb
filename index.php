<?php
require 'util/setup.php';
$page = new Page('Products', 'components/products.php', ['script.js', 'scripts/darkmode.js']);
$page->allow_indexing = true;
$page->has_navbar = true;
$page->has_feet = true;
require 'components/base.php';
?>
