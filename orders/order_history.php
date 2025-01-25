<?php
// Check if vendor
if ($_SESSION['vendor'] ?? false) {
    header('Location: ../shop');
    exit();
}
?>
        <main>
            <h1>Your orders</h1>
        </main>
