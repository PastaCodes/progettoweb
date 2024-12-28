<?php
require '../util/setup.php';
$page->title = 'Product Info';
$page->body = 'components/product_info.php';
$page->allow_indexing = false;
$page->has_navbar = true;
$page->has_feet = true;
$page->stylesheets[] = 'style.css';
require '../components/base.php';
?>
