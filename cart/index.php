<?php
require_once '../util/setup.php';
$page->title = 'Your Cart';
$page->body = 'cart/cart.php';
$page->has_navbar = true;
$page->has_feet = true;
$page->scripts[] = Script::external('scripts/cart.js', 'module');
require_once '../components/base.php';
?>
