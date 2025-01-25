<?php
// To use session vars
session_start();
if (isset($_SESSION['username'])) {
    require_once '../util/db.php';
    // Order request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
        require_once '../classes/Cart.php';
        $order_id = $database->insert(
            table: 'order_request',
            data: [
                ['username' => $_SESSION['username']]
            ]
        )[0];
        $order_entries = [];
        $cart = Cart::load_from_cookie();
        if (empty($cart->entries)) {
            header('Location: ../cart');
            exit();
        }
        $cart->fetch_details();
        foreach ($cart->entries as $cart_entry) {
            $order_entry = [
                'order_request' => $order_id,
                'quantity' => $cart_entry->quantity,
                'price' => $cart_entry->entry_price()
            ];
            if ($cart_entry instanceof ProductEntry) {
                $order_entry['product_base'] = $cart_entry->product->base->code_name;
                $order_entry['bundle'] = null;
                $order_entry['variant'] = $cart_entry->product->variant?->code_suffix;
            } else if ($cart_entry instanceof BundleEntry) {
                $order_entry['product_base'] = null;
                $order_entry['bundle'] = $cart_entry->bundle->code_name;
                $order_entry['variant'] = $cart_entry->bundle->selected_suffix;
            }
            $order_entries[] = $order_entry;
        }
        $database->insert(
            table: 'order_entry',
            data: $order_entries
        );
        setcookie('cart', '', -1);
        header('Location: ../orders');
        exit();
    }
    // Delete notification request
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['delete_notification'])) {
        $database->delete(
            table: 'notification',
            filters: [
                'id' => $_GET['delete_notification'],
                'username' => $_SESSION['username']
            ]
        );
        exit();
    }
    // Create notification request
    if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['title'])) {
        if (isset($_GET['recipient']) && $_GET['recipient'] !== $_SESSION['username'] && (!isset($_SESSION['vendor']) || !$_SESSION['vendor'])){
            exit;
        }
        $database->insert(
            table: 'notification',
            data: [
                [
                    'title' => $_GET['title'],
                    'content' => $_GET['content'],
                    'username' => $_GET['recipient'] ?? $_SESSION['username']
                ]
            ]
        );
        exit();
    }
}
header('Location: ..');
exit();
?>
