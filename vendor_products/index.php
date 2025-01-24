<?php
require_once '../util/setup.php';
$page->title = 'Modify Products - IsiFitGems';
$page->body = 'vendor_products/modify_products.php';
$page->has_navbar = true;
$page->has_feet = true;
$page->scripts = [Script::external('scripts/table_collapsable.js', 'module')];
require_once '../components/base.php';
?>
