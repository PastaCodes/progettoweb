<?php
// Check if vendor
if (!isset($_SESSION['vendor'])) {
    header('Location: ../shop');
    exit();
}
require_once '../util/db.php';

$bundle_id = null;
$button_action = null;
if (isset($_POST['button_action'])) {
    $params = explode(':', $_POST['button_action']);
    $button_action = $params[0];
    if (isset($params[1])) {
        $bundle_id = $params[1];
    }
}
if ($button_action === 'update_bundle' || $button_action === 'create_bundle') {
    // Get the data from the form
    $bundle_id = $_POST['code_name'];
    $display_name = $_POST['display_name'];
    $multiplier = $_POST['multiplier'];
    // Update that table's entry
    if ($button_action === 'create_bundle') {
        $database->insert(
            table: 'bundle',
            data: [
                [
                    'code_name' => $bundle_id, 
                    'display_name' => $display_name, 
                    'multiplier' => $multiplier, 
                ]
            ]
        );
    } else {
        $database->update(
            table: 'bundle',
            data: [
                'code_name' => $bundle_id, 
                'display_name' => $display_name, 
                'multiplier' => $multiplier, 
            ],
            filters: ['bundle.code_name' => $bundle_id]
        );
    }
} else if ($button_action == 'delete_bundle') {
    // Quick and easy query to delete a bundle from the db
    $bundle_id = $_POST['code_name'];
    $database->delete(
        table: 'bundle',
        filters: ['code_name' => $bundle_id]
    );
} else if ($button_action == 'update_bundle_product' || $button_action == 'create_bundle_product') {
    // Get the data from the form
    $product_id = $_POST['bundle_product'];
    $ordinal = $_POST['product_ordinal'];
    // Update that table's entry
    if ($button_action == 'create_bundle_product') {
        $database->insert(
            table: 'product_in_bundle',
            data: [
                [
                    'base' => $product_id, 
                    'bundle' => $bundle_id, 
                    'ordinal' => $ordinal, 
                ]
            ]
        );
    } else {
        $database->update(
            table: 'product_in_bundle',
            data: [
                'base' => $product_id, 
                'bundle' => $bundle_id, 
                'ordinal' => $ordinal, 
            ],
            filters: [
                'product_in_bundle.bundle' => $bundle_id,
                'product_in_bundle.base' => $product_id
            ]
        );
    }
} else if ($button_action == 'delete_bundle_product') {
    // Quick and easy query to delete a bundle from the db
    $product_id = $_POST['bundle_product'];
    $database->delete(
        table: 'product_in_bundle',
        filters: [
            'base' => $product_id,
            'bundle' => $bundle_id
        ]
    );    
}

$product_data = $database->find(
    table: 'product_base'
);
$bundle_data = $database->find(
    table: 'bundle',
    joins: [
        [
            'type' => 'LEFT',
            'table' => 'product_in_bundle',
            'on' => 'bundle = code_name' 
        ]
    ],
    options: [
        'order_by' => [
            'code_name' => 'ASC',
            'product_in_bundle.ordinal' => 'ASC'
        ]
    ]
);
$bundles = [];
foreach ($bundle_data as $bundle_row) {
    if (!isset($bundles[$bundle_row['code_name']])) {
        $bundles[$bundle_row['code_name']] = [
            'id' => $bundle_row['code_name'],
            'display_name' => $bundle_row['display_name'],
            'multiplier' => $bundle_row['multiplier'],
            'products' => []
        ];
    }
    if ($bundle_row['base']) {
        $bundles[$bundle_row['code_name']]['products'][] = [
            'id' => $bundle_row['base'],
            'ordinal' => $bundle_row['ordinal']
        ];
    }
}

?>
    <main>
        <form>
            <h2>Add new product</h2>
        </form>
        <table>
            <thead>
                <tr>
                    <th scope="col">bundle_id</th>
                    <th scope="col">display_name</th>
                    <th scope="col">multiplier / ordinal</th>
                    <th scope="col" colspan="3">actions</th>
                </tr>
            </thead>
            <tbody>
<?php foreach ($bundles as $bundle): ?>
                <tr>
                    <td>
                        <input form="<?= $bundle['id'] ?>" minlength="1" maxlength="255" type="text" name="code_name" value="<?= $bundle['id'] ?>" placeholder="Bundle id" required="required">
                    </td>
                    <td>
                        <input form="<?= $bundle['id'] ?>" minlength="1" maxlength="255" type="text" name="display_name" value="<?= $bundle['display_name'] ?>" placeholder="Display name" required="required">
                    </td>
                    <td>
                        <input form="<?= $bundle['id'] ?>" min="0.01" max="0.99" step="0.01" type="number" name="multiplier" value="<?= $bundle['multiplier'] ?>" required="required">
                    </td>
                    <td>
                        <button data-show="<?= $bundle['id'] ?>">&#9660;</button>
                    </td>
                    <td>
                        <button form="<?= $bundle['id'] ?>" type="submit" name="button_action" value="update_bundle">Upd</button>
                    </td>
                    <td>
                        <form id="<?= $bundle['id'] ?>" method="POST">
                            <button type="submit" name="button_action" value="delete_bundle">Del</button>
                        </form>
                    </td>
                </tr>
<?php foreach ($bundle['products'] as $bundle_product): ?>
                <tr data-parent="<?= $bundle['id'] ?>">
                    <td colspan="2">
                        <select form="<?= $bundle['id'] . '-' . $bundle_product['id'] ?>" name="bundle_product">
<?php foreach ($product_data as $product): ?>
                            <option value="<?= $product['code_name'] ?>" <?php if ($bundle_product['id'] == $product['code_name']): ?>selected="selected"<?php endif ?>><?= $product['display_name'] ?></option>
<?php endforeach ?>
                        </select>
                    </td>
                    <td>
                        <input form="<?= $bundle['id']. '-' . $bundle_product['id'] ?>" type="number" name="product_ordinal" value="<?= $bundle_product['ordinal'] ?>" required="required">
                    </td>
                    <td></td>        
                    <td>
                        <button form="<?= $bundle['id']. '-' . $bundle_product['id'] ?>" type="submit" name="button_action" value="update_bundle_product:<?= $bundle['id'] ?>">Upd</button>
                    </td>
                    <td>
                        <form id="<?= $bundle['id'] . '-' . $bundle_product['id'] ?>" method="POST">
                            <button type="submit" name="button_action" value="delete_bundle_product:<?= $bundle['id'] ?>">Del</button>
                        </form>
                    </td>
                </tr>
<?php endforeach ?>
                <tr data-parent="<?= $bundle['id'] ?>">
                    <td colspan="2">
                        <select form="<?= $bundle['id'] ?>-new-bundle-product" name="bundle_product">
<?php foreach ($product_data as $product): ?>
                            <option value="<?= $product['code_name'] ?>"><?= $product['display_name'] ?></option>
<?php endforeach ?>
                        </select>
                    </td>
                    <td>
                        <input form="<?= $bundle['id'] ?>-new-bundle-product" type="number" name="product_ordinal" required="required">
                    </td>
                    <td colspan="3">
                        <form id="<?= $bundle['id'] ?>-new-bundle-product" method="POST">
                            <button form="<?= $bundle['id'] ?>-new-bundle-product" type="submit" name="button_action" value="create_bundle_product:<?= $bundle['id'] ?>">Add</button>
                        </form>
                    </td>
                </tr>
<?php endforeach ?>
                <tr>
                    <td>
                        <input form="new-bundle" minlength="1" maxlength="255" type="text" name="code_name" placeholder="Bundle id" required="required">
                    </td>
                    <td>
                        <input form="new-bundle" minlength="1" maxlength="255" type="text" name="display_name" placeholder="Display name" required="required">
                    </td>
                    <td>
                        <input form="new-bundle" min="0.01" max="0.99" step="0.01" type="number" name="multiplier" required="required">
                    </td>
                    <td colspan="3">
                        <form id="new-bundle" method="POST">
                            <button type="submit" name="button_action" value="create_bundle">Add</button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </main>
