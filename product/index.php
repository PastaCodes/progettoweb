<?php
require_once '../util/setup.php';
$page->title = 'Product Info';
$page->body = 'components/product_info.php';
$page->allow_indexing = false;
$page->has_navbar = true;
$page->has_feet = true;
$page->scripts[] = Script::external('scripts/cart.js', 'module');
require_once '../components/base.php';
?>
