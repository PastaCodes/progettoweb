<?php
// Check if vendor
if (!isset($_SESSION['vendor'])) {
    header('Location: ../shop');
    exit();
}
require_once '../util/db.php';

$product_data = $database->find(
    table: 'product_base'
);
$bundle_data = $database->find(
    table: 'bundle',
    joins: [
        [
            'type' => 'INNER',
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
    $bundles[$bundle_row['code_name']]['products'][] = [
        'id' => $bundle_row['base'],
        'ordinal' => $bundle_row['ordinal']
    ];
}

/* TODO:
 * Add functionality
 */
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
                    <th scope="col">multiplier</th>
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
                        <form id="<?= $bundle['id'] ?>" action="vendor_bundles" method="POST">
                            <button type="submit" name="button_action" value="delete_bundle">Del</button>
                        </form>
                    </td>
                </tr>
<?php foreach ($bundle['products'] as $bundle_product): ?>
                <tr data-parent="<?= $bundle['id'] ?>">
                    <td colspan="2">
                        <select form="<?= $bundle['id'] ?>" name="product_0">
<?php foreach ($product_data as $product): ?>
                            <option value="<?= $product['code_name'] ?>" <?php if ($bundle_product['id'] == $product['code_name']): ?>selected="selected"<?php endif ?>><?= $product['display_name'] ?></option>
<?php endforeach ?>
                        </select>
                    </td>
                    <td>
                        <input form="<?= $bundle['id'] ?>" type="number" name="product_ordinal" value="<?= $bundle_product['ordinal'] ?>" required="required">
                    </td>
                    <td colspan="3">
                        <form id="<?= $bundle['id'] ?>" action="vendor_bundles" method="POST">
                            <button type="submit" name="button_action" value="delete_bundle_product">Del</button>
                        </form>
                    </td>
                </tr>
<?php endforeach ?>
                <tr data-parent="<?= $bundle['id'] ?>">
                    <td colspan="2">
                        <select form="<?= $bundle['id'] ?>" name="new_product">
<?php foreach ($product_data as $product): ?>
                            <option value="<?= $product['code_name'] ?>"><?= $product['display_name'] ?></option>
<?php endforeach ?>
                        </select>
                    </td>
                    <td>
                        <input form="<?= $bundle['id'] ?>" type="number" name="product_ordinal" required="required">
                    </td>
                    <td colspan="3">
                        <form id="<?= $bundle['id'] ?>" action="vendor_bundles" method="POST">
                            <button type="submit" name="button_action" value="add_bundle_product">Add</button>
                        </form>
                    </td>
                </tr>
<?php endforeach ?>
                <tr>
                    <td>
                        <input form="new_bundle" minlength="1" maxlength="255" type="text" name="code_name" placeholder="Bundle id" required="required">
                    </td>
                    <td>
                        <input form="new_bundle" minlength="1" maxlength="255" type="text" name="display_name" placeholder="Display name" required="required">
                    </td>
                    <td>
                        <input form="<?= $bundle['id'] ?>" min="0.01" max="0.99" step="0.01" type="number" name="multiplier" required="required">
                    </td>
                    <td colspan="3">
                        <form id="new_bundle" action="vendor_bundles" method="POST">
                            <button type="submit" name="button_action" value="create_bundle">Add</button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </main>
