<?php
require_once '../util/setup.php';
$page->title = 'Modify Bundles - IsiFitGems';
$page->body = 'vendor_bundles/modify_bundles.php';
$page->has_navbar = true;
$page->has_feet = true;
$page->scripts = [Script::external('scripts/table_collapsable.js', 'module')];
require_once '../components/base.php';
?>
