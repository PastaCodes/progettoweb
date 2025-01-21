<?php
require_once '../util/setup.php';
$page->title = 'Shop - IsiFitGems';
$page->body = 'product/product_info.php';
$page->allow_indexing = false;
$page->has_navbar = true;
$page->has_feet = true;
$page->scripts[] = Script::external('scripts/cart.js', 'module');
require_once '../components/base.php';
?>
