<?php
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
                    <th scope="col" colspan="2">actions</th>
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
                        <input form="<?= $bundle->code_name ?>" type="submit" value="Upd">
                    </td>
                    <td>
                        <form id="<?= $bundle->code_name ?>" action="vendor_bundles" method="POST">
                            <input type="submit" value="Del">
                        </form>
                    </td>
                </tr>
<?php endforeach ?>
            </tbody>
        </table>
    </main>
