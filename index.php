<?php
require 'util/setup.php';
$page->title = 'Products';
$page->body = 'components/products.php';
$page->allow_indexing = true;
$page->has_navbar = true;
$page->has_feet = true;
$page->stylesheets[] = 'style.css';
$page->scripts[] = Script::external('script.js');
require 'components/base.php';
?>
