<?php
require_once '../classes/Product.php';
require_once '../classes/Bundle.php';
require_once '../classes/CartEntry.php';
require_once '../util/db.php';
require_once '../util/format.php';

// Check if vendor
if ($_SESSION['vendor'] ?? false) {
    header('Location: ../shop');
    exit();
}

$orders = [];
if (isset($_SESSION['username'])) {
    $orders_data = $database->find(
        table: 'order_request',
        joins: [
            [
                'type' => 'INNER',
                'table' => 'order_total',
                'on' => 'order_total.order_request = order_request.id'
            ],
            [
                'type' => 'INNER',
                'table' => 'order_entry',
                'on' => 'order_entry.order_request = order_request.id'
            ],
            [
                'type' => 'LEFT',
                'table' => 'product_base',
                'on' => 'product_base.code_name = order_entry.product_base'
            ],
            [
                'type' => 'LEFT',
                'table' => 'bundle',
                'on' => 'bundle.code_name = order_entry.bundle'
            ],
            [
                'type' => 'LEFT',
                'table' => 'product_in_bundle',
                'on' => 'product_in_bundle.bundle = bundle.code_name'
            ],
            [
                'type' => 'LEFT',
                'table' => 'bundle_variant',
                'on' => 'bundle_variant.bundle = bundle.code_name and bundle_variant.code_suffix = order_entry.variant'
            ],
            [
                'type' => 'LEFT',
                'table' => 'product_variant',
                'on' => '(product_variant.base = product_base.code_name and product_variant.code_suffix = order_entry.variant) or (product_variant.base = product_in_bundle.base and product_variant.code_suffix = bundle_variant.code_suffix)'
            ]
        ],
        filters: ['username' => $_SESSION['username']],
        options: [
            'group_by' => 'order_entry.id, bundle.code_name',
            'order_by' => [
                'created_at' => 'DESC'
            ]
        ]
    );
    foreach ($orders_data as $order_data) {
        $order_id = $order_data['order_request.id'];
        if (!isset($orders[$order_id])) {
            $orders[$order_id] = [
                'status' => $order_data['order_status'],
                'total' => $order_data['total'],
                'timestamp' => $order_data['created_at'],
                'entries' => []
            ];
        }
        if (isset($order_data['product_base'])) {
            $product = Product::from($order_data['product_base'], $order_data['variant'] ?? null);
            $product->price = $order_data['price'];
            $product->base->display_name = $order_data['product_base.display_name'];
            if (isset($order_data['product_variant.display_name'])) {
                $product->variant->display_name = $order_data['product_variant.display_name'];
            }
            $entry = new ProductEntry($product, $order_data['quantity']);
        } else {
            $bundle = new Bundle($order_data['order_entry.bundle'], $order_data['variant'] ?? null);
            $bundle->display_name = $order_data['bundle.display_name'];
            $bundle->price_with_discount = $order_data['price'];
            if ($order_data['variant'] !== null) {
                $variant = new ProductVariant('variant');
                $variant->display_name = $order_data['product_variant.display_name'];
                $bundle->variants = [new BundleVariant($variant)];
            }
            $entry = new BundleEntry($bundle, $order_data['quantity']);
        }
        $orders[$order_id]['entries'][] = $entry;
    }
}

function print_entry(CartEntry $entry): string {
    $html = $entry->quantity . 'x ';
    if ($entry instanceof ProductEntry) {
        $html .= $entry->product->base->display_name;
        if ($entry->product->variant !== null) {
            $html .= ' (' . $entry->product->variant->display_name . ')';
        }
    } else if ($entry instanceof BundleEntry) {
        $html .= $entry->bundle->display_name;
        if (!empty($entry->bundle->variants)) {
            $html .= ' (' . $entry->bundle->variants[0]->variant->display_name . ')';
        }
    }
    $html .= ' ' . format_price($entry->entry_price());
    return $html;
}
?>
        <main>
            <section>
                <h1>Your orders</h1>
<?php if (isset($_SESSION['username'])): ?>
<?php if (empty($orders)): ?>
                <p>You haven't made any orders yet. <a href="shop">Continue shopping</a>.</p>
<?php else: ?>
                <ul>
<?php foreach ($orders as $order): ?>
                    <li>
                        <p><?= $order['timestamp'] ?></p>
                        <progress min="0" max="2" value="<?= array_search($order['status'], ['pending', 'shipped', 'delivered']) ?>"></progress>
                        <p>Status: <?= ucfirst($order['status']) ?></p>
                        <h2>Items:</h2>
                        <ul>
<?php foreach ($order['entries'] as $entry): ?>
                            <li><?= print_entry($entry) ?></li>
<?php endforeach ?>
                        </ul>
                        <p>Total: <?= format_price($order['total']) ?></p>
                    </li>
<?php endforeach ?>
                </ul>
<?php endif ?>
<?php else: ?>
                <p><a href="login">Login to see your orders</a></p>
<?php endif ?>
            </section>
        </main>
