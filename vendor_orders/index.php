<?php
require_once '../util/setup.php';
$page->title = 'Current Orders - IsiFitGems';
$page->body = 'vendor_orders/vendor_orders.php';
$page->has_navbar = true;
$page->has_feet = true;
$page->stylesheets[] = 'vendor_products/style.css';
$page->scripts[] = Script::external('scripts/table_collapsable.js', 'module');
require_once '../util/base.php';
?>
