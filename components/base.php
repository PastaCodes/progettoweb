<?php
ob_start();
require_once __DIR__ . '/../' . $page->body;
$body = ob_get_clean();
$internal_script = 'const chosenTheme = localStorage.getItem(\'theme\'); if (chosenTheme !== null) { document.documentElement.setAttribute(\'data-theme\', chosenTheme); }';
$accessibility = json_decode($_COOKIE['accessibility'] ?? '{}');
$filters = [];
if ($accessibility->high_contrast ?? false) {
    $filters[] = 'contrast(120%)';
}
if ($accessibility->grayscale ?? false) {
    $filters[] = 'grayscale(100%)';
}
if ($accessibility->reduced_strain ?? false) {
    $filters[] = 'contrast(90%) brightness(70%) sepia(30%) saturate(120%)';
}
if (!empty($filters)) {
    $internal_script .= ' document.documentElement.style.filter = \'' . implode(' ', $filters) .'\';';
}
if ($accessibility->larger_text ?? false) {
    $internal_script .= ' document.documentElement.style.setProperty(\'--font-size-adjust\', 1.3);';
}
$page->scripts[] = Script::internal($internal_script);
// Notification stuff
$notifications = null;
if (isset($_SESSION['username'])) {
    require_once __DIR__ . '/../classes/Notification.php';
    $notifications = Notification::fetch_all_of($_SESSION['username']);
}
// Track last page
if ($page->track_page_cookie) {
    $clean_url = strtok($_SERVER['REQUEST_URI'], '?') . (parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY) ? '?' . http_build_query(array_diff_key($_GET, ['logout' => ''])) : '');
    setcookie('last_page', $clean_url, time() + 3600, '/');
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="application-name" content="IsiFitGems">
        <meta name="description" content="Fashion accessories for Unibo students in Cesena.">
        <meta name="keywords" content="fashion, accessories, Unibo, Cesena, shop, e-commerce">
        <meta name="author" content="Luca Palazzini, Marco Buda">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="<?= SETTINGS['theme-color'] ?>">
<?php if ($page->allow_indexing): ?>
        <meta name="robots" content="index, follow">
<?php else: ?>
        <meta name="robots" content="none">
<?php endif ?>
        <base href="<?= $base_url ?>" target="_self">
<?php foreach ($page->scripts as $script): ?>
        <?= $script->to_script_tag() ?>
<?php endforeach ?>
<?php foreach ($page->stylesheets as $stylesheet): ?>
        <link rel="stylesheet" type="text/css" href="<?= $stylesheet ?>">
<?php endforeach ?>
        <link rel="icon" type="image/x-icon" href="assets/isi.svg">
<?php foreach ($page->prefetch as $resource): ?>
        <link rel="prefetch" href="<?= $resource ?>">
<?php endforeach ?>
        <title><?= $page->title ?></title>
    </head>
    <body>
<?php if ($page->has_navbar): ?>
        <header>
            <nav>
                <ul>
                    <li>
                        <a href="">
                            <h1>
                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="IsiFitGems logo">
                                    <use href="assets/isi.svg#isi"></use>
                                </svg>
                                IsiFitGems
                            </h1>
                        </a>
                    </li>
                    <li>
                        <a href="shop">Our products</a>
                    </li>
                    <li>
                        <a href="bundles">Bundles</a>
                    </li>
                    <li>
                        <a>Support</a>
                    </li>
<?php if (isset($_SESSION['vendor']) && $_SESSION['vendor']): ?>
                    <li>
                        <a href="vendor_orders">Orders</a>
                    </li>
                    <li>
                        <a href="vendor_products">Products</a>
                    </li>
                    <li>
                        <a href="vendor_bundles">Bundles</a>
                    </li>
<?php else: ?>
                    <li>
                        <a href="">Your orders <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label=""><use href="assets/orders.svg#orders"></use></svg></a>
                    </li>
                    <li>
                        <a href="cart">Your cart <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label=""><use href="assets/cart.svg#cart"></use></svg></a>
                    </li>
<?php endif ?>
                    <li>
<?php if (isset($_SESSION['username'])): ?>
                        <a href="login?logout=true" data-tooltip="Logged in as <?= $_SESSION['username'] ?>" data-placement="bottom">Logout <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label=""><use href="assets/account.svg#account"></use></svg></a>
<?php else: ?>
                        <a href="login">Login <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label=""><use href="assets/account.svg#account"></use></svg></a>
<?php endif ?>
                    </li>
                </ul>
            </nav>
        </header>
<?php endif ?>
        <ul>
            <li>
                <button title="Switch to dark theme">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                        <use href="assets/lightmode.svg#lightmode"></use>
                    </svg>
                </button>
            </li>
            <li>
                <button <?php if ($notifications === null): ?>title="Login to see your notifications" disabled="disabled"<?php else: ?>title="Show notifications"<?php endif ?>>
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                        <use href="assets/notifications.svg#notifications"></use>
                    </svg>
                </button>
            </li>
            <li>
                <button title="Accessibility options">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                        <use href="assets/accessibility.svg#accessibility"></use>
                    </svg>
                </button>
            </li>
        </ul>
<?= $body ?>
<?php if ($page->has_feet): ?>
        <footer>
            <ul>
                <li>
                    <a>Terms and Conditions</a>
                </li>
                <li>
                    <a>Privacy Policy</a>
                </li>
                <li>
                    &copy; 2025 IsiFitGems s.r.l.
                </li>
            </ul>
        </footer>
<?php endif ?>
<?php if ($notifications !== null): ?>
        <dialog>
            <article>
                <header>
                    <h2>Notifications</h2>
                </header>
                <label for="hide-seen">
                    <input id="hide-seen" name="hide-seen" type="checkbox">
                    Unread only
                </label>
<?php if (empty($notifications)): ?>
                <p>You have no new notifications</p>
<?php else: ?>
                <ul>
<?php foreach ($notifications as $notification): ?>
                    <li data-id="<?= $notification->id ?>" data-timestamp="<?= $notification->created_at->format('Y-m-d H:i:s') ?>">
                        <h3><?= $notification->title ?></h3>
                        <p><?= $notification->content ?></p>
                        <p>Moments ago</p>
                        <button title="Mark as read">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                                <use href="assets/unread.svg#unread"></use>
                            </svg>
                        </button>
                        <button title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                                <use href="assets/remove.svg#remove"></use>
                            </svg>
                        </button>
                    </li>
<?php endforeach ?>
                </ul>
<?php endif ?>
                <footer>
                    <button>Close</button>
                </footer>
            </article>
        </dialog>
<?php endif ?>
        <dialog>
            <article>
                <header>
                    <h2>Accessibility options</h2>
                </header>
                <fieldset>
                    <legend>Filters</legend>
                    <label for="high-contrast">
                        <input id="high-contrast" name="high-contrast" type="checkbox" role="switch"<?php if ($accessibility->high_contrast ?? false): ?> checked="checked" <?php endif ?>>    
                        High contrast
                    </label>
                    <label for="grayscale">
                        <input id="grayscale" name="grayscale" type="checkbox" role="switch"<?php if ($accessibility->grayscale ?? false): ?> checked="checked" <?php endif ?>>
                        Grayscale
                    </label>
                    <label for="reduced-strain">
                        <input id="reduced-strain" name="reduced-strain" type="checkbox" role="switch"<?php if ($accessibility->reduced_strain ?? false): ?> checked="checked" <?php endif ?>>
                        Reduced eye strain
                    </label>
                </fieldset>
                <fieldset>
                    <legend>Other</legend>
                    <label for="larger-text">
                        <input id="larger-text" name="larger-text" type="checkbox" role="switch"<?php if ($accessibility->larger_text ?? false): ?> checked="checked" <?php endif ?>>
                        Larger text
                    </label>
                </fieldset>
                <footer>
                    <button>Confirm</button>
                </footer>
            </article>
        </dialog>
    </body>
</html>
