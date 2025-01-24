<?php
// Check if vendor
if (!isset($_SESSION['vendor'])) {
    header('Location: ../shop');
    exit();
}
require_once '../classes/Bundle.php';

$bundles = Bundle::fetch_bundles();

/* TODO:
 * Add the other bundle parameters
 * Add variants as submenu or modal of row to edit variant data?
 * Add current data to category, short_description and is_standalone
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
                        <input form="<?= $bundle->code_name ?>" minlength="1" maxlength="255" type="text" name="code_name" value="<?= $bundle->code_name ?>" placeholder="Bundle id" required="required">
                    </td>
                    <td>
                        <input form="<?= $bundle->code_name ?>" minlength="1" maxlength="255" type="text" name="display_name" value="<?= $bundle->display_name ?>" placeholder="Display name" required="required">
                    </td>
                    <td>
                        <input form="<?= $bundle->code_name ?>" min="0.01" max="0.99" step="0.01" type="number" name="multiplier" value="0.01" required="required">
                    </td>
                    <td>
                        <button data-show="<?= $bundle->code_name ?>">&#9660;</button>
                    </td>
                    <td>
                        <button form="<?= $bundle->code_name ?>" type="submit" name="button_action" value="update_bundle">Upd</button>
                    </td>
                    <td>
                        <form id="<?= $bundle->code_name ?>" action="vendor_bundles" method="POST">
                            <button type="submit" name="button_action" value="delete_bundle">Del</button>
                        </form>
                    </td>
                </tr>
<?php if (true): ?>
    <tr data-parent="<?= $bundle->code_name ?>" colspan="6">
        <td>Tette</td>
    </tr>
<?php endif ?>
<?php endforeach ?>
                <tr>
                    <td>
                        <input form="new_bundle" minlength="1" maxlength="255" type="text" name="code_name" placeholder="Bundle id" required="required">
                    </td>
                    <td>
                        <input form="new_bundle" minlength="1" maxlength="255" type="text" name="display_name" placeholder="Display name" required="required">
                    </td>
                    <td>
                        <input form="<?= $bundle->code_name ?>" min="0.01" max="0.99" step="0.01" type="number" name="multiplier" required="required">
                    </td>
                    <td></td>
                    <td></td>
                    <td>
                        <form id="new_bundle" action="vendor_bundles" method="POST">
                            <button type="submit" name="button_action" value="create_bundle">Add</button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </main>
