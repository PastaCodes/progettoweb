<?php
// Check if vendor
if (!isset($_SESSION['vendor']) || $_SESSION['vendor'] !== true) {
    header('Location: ../shop');
    exit();
}
require_once '../util/db.php';

if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $database->update(
        table: 'order_request',
        data: ['order_status' => $status],
        filters: ['id' => $order_id]
    );
}

$order_statues = ['pending', 'shipped', 'delivered'];
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
        ]
    ]
);
$orders = [];
foreach ($orders_data as $order_data) {
    $order_id = $order_data['order_request.id'];
    if (!isset($orders[$order_id])) {
        $orders[$order_id] = [
            'id' => $order_id,
            'user' => $order_data['username'],
            'status' => $order_data['order_status'],
            'total' => $order_data['total'],
            'contents' => []
        ];
    }
    if ($order_data['product_base'] || $order_data['bundle']) {
        $orders[$order_id]['contents'][] = [
            'id' => ($order_data['product_base'] ?? $order_data['bundle'] ?? '') . ($order_data['variant'] ? ('_' . $order_data['variant']) : ''),
            'quantity' => $order_data['quantity'],
            'price' => $order_data['price'],
            'type' => $order_data['product_base'] ? 'Product' : 'Bundle'
        ];
    }
}

?>
    <main>
        <table>
            <thead>
                <tr>
                    <th scope="col">order_id</th>
                    <th scope="col">user</th>
                    <th scope="col">status</th>
                    <th scope="col">details</th>
                    <th scope="col">actions</th>
                </tr>
            </thead>
            <tbody>
<?php foreach ($orders as $order): ?>
                <tr>
                    <td>
                        <p><?= $order['id'] ?></p>
                    </td>
                    <td>
                        <p><?= $order['user'] ?></p>
                    </td>
                    <td>
                        <select form="order-<?= $order['id'] ?>" name="status">
<?php foreach ($order_statues as $order_status): ?>
                            <option value="<?= $order_status ?>" <?php if ($order['status'] === $order_status): ?> selected="selected"<?php endif ?>><?= $order_status ?></option>
<?php endforeach ?>
                        </select>
                    </td>
                    <td>
                        <button data-show="id-<?= $order['id'] ?>">&#9660;</button>
                    </td>
                    <td>
                        <form id="order-<?= $order['id'] ?>" method="POST">
                            <button type="submit" name="order_id" value="<?= $order['id'] ?>">Update Status</button>
                        </form>
                    </td>
                </tr>
                <tr data-parent="id-<?= $order['id'] ?>">
                    <td colspan="3">
                        <ul>
<?php foreach ($order['contents'] as $order_element): ?>
                            <li>
                                <p>
                                    <span><?= $order_element['quantity'] ?>x</span>
                                    <span><?= $order_element['id'] ?></span>
                                </p>
                                <p><?= $order_element['type'] ?></p>
                                <p><?= $order_element['price'] ?> &euro;</p>
                            </li>
<?php endforeach ?>
                            <li>
                                <p><b>Total: </b></p>
                                <p><?= $order['total'] ?> &euro;</p>
                            </li>
                        </ul>
                    </td>                    
                    <td colspan="2"></td>
                </tr>
<?php endforeach ?>
            </tbody>
        </table>
    </main>
