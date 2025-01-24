<?php
// Check if vendor
if (!isset($_SESSION['vendor'])) {
    header('Location: ../shop');
    exit();
}

require_once '../classes/Product.php';
require_once '../classes/Category.php';

$button_action = null;
if (isset($_POST['button_action'])) {
    $button_action = $_POST['button_action'];
}
if ($button_action === 'update_product' || $button_action === 'create_product') {
    // Get the data from the form
    $product_id = $_POST['base_code_name'];
    $display_name = $_POST['base_display_name'];
    $base_price = $_POST['base_price'];
    $category = $_POST['category'];
    $short_description = $_POST['short_description'];
    // Ensure the boolean is an integer
    $is_standalone = ($_POST['is_standalone'] ?? false) ? 1 : 0;
    // Update that table's entry
    if ($button_action === 'create_product') {
        $database->insert(
            table: 'product_base',
            data: [
                'code_name' => $product_id, 
                'display_name' => $display_name, 
                'category' => $category, 
                'short_description' => $short_description,
                'price_base' => $base_price,
                'standalone' => $is_standalone
            ]
        );
    } else {
        $database->update(
            table: 'product_base',
            data: [
                'code_name' => $product_id, 
                'display_name' => $display_name, 
                'category' => $category, 
                'short_description' => $short_description,
                'price_base' => $base_price,
                'standalone' => $is_standalone
            ],
            filters: ['product_base.code_name' => $product_id]
        );
    }
} else if ($button_action == 'delete_product') {
    // Quick and easy query to delete a product from the db
    $product_id = $_POST['base_code_name'];
    $database->delete(
        table: 'product_base',
        filters: ['code_name' => $product_id]
    );
} else if ($button_action == 'update_variant' || $button_action == 'create_variant') {
    // Get the data from the form
    
    // FIXME: Currently broken query, as it also needs the product_base_id
    $variant_id = $_POST['variant_code_name'];
    $display_name = $_POST['variant_display_name'];
    $color = substr($_POST['variant_color'], 1);
    $ordinal = $_POST['variant_ordinal'];
    $price_override = $_POST['variant_price'];
    // Update that table's entry
    if ($button_action === 'create_variant') {
        $database->insert(
            table: 'product_variant',
            data: [
                'code_suffix' => $variant_id,
                'ordinal' => $ordinal,
                'display_name' => $display_name,
                'color' => $color,
                'price_override' => $price_override
            ],
        );
    } else {
        $database->update(
            table: 'product_variant',
            data: [
                'code_suffix' => $variant_id,
                'ordinal' => $ordinal,
                'display_name' => $display_name,
                'color' => $color,
                'price_override' => $price_override
            ],
            filters: ['product_variant.code_suffix' => $variant_id]
        );
    }
} else if ($button_action == 'delete_variant') {
    // Quick and easy query to delete a product from the db
    
    // FIXME: Currently broken query, as it also needs the product_base_id
    $variant_id = $_POST['variant_code_name'];
    $database->delete(
        table: 'product_variant',
        filters: ['code_suffix' => $variant_id]
    );
}

$products = Product::fetch_products();
$categories = Category::fetch_all();

/* TODO:
 * Add current data to category, short_description and is_standalone (also price and ordinal for variant)
 * cant be asked to do even simple queries ;-;
 */
?>
    <main>
        <table>
            <thead>
                <tr>
                    <th scope="col">base_id</th>
                    <th scope="col">display_name</th>
                    <th scope="col">category / color</th>
                    <th scope="col">short_description / ordinal</th>
                    <th scope="col">price</th>
                    <th scope="col">is_standalone</th>
                    <th scope="col" colspan="3">actions</th>
                </tr>
            </thead>
            <tbody>
<?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <input form="<?= $product->base->code_name ?>" minlength="1" maxlength="255" type="text" name="base_code_name" value="<?= $product->base->code_name ?>" placeholder="Product id" required="required">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name ?>" minlength="1" maxlength="255" type="text" name="base_display_name" value="<?= $product->base->display_name ?>" placeholder="Display name" required="required">
                    </td>
                    <td>
                        <select form="<?= $product->base->code_name ?>" name="category">
<?php foreach ($categories as $category): ?>
                            <option value="<?= $category['display_name'] ?>"><?= $category['display_name'] ?></option>
<?php endforeach ?>
                        </select>
                    </td>
                    <td>
                        <textarea form="<?= $product->base->code_name ?>" minlength="1" maxlength="255" name="short_description" placeholder="Product description" required="required"></textarea>
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name ?>" step="0.01" type="number" name="base_price" value="0" required="required">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name ?>" type="checkbox" name="is_standalone">
                    </td>
                    <td>
<?php if ($product->base->variants): ?>
                        <button data-show="<?= $product->base->code_name ?>">V</button>
<?php endif ?>
                    </td>
                    <td>
                        <button form="<?= $product->base->code_name ?>" type="submit" name="button_action" value="update_product">Update</button>
                    </td>
                    <td>
                        <form id="<?= $product->base->code_name ?>" method="POST">
                            <button form="<?= $product->base->code_name ?>" type="submit" name="button_action" value="delete_product">Delete</button>
                        </form>
                    </td>
                </tr>
<?php foreach ($product->base->variants as $variant): ?>
                <tr data-parent="<?= $product->base->code_name ?>">
                    <td>
                        <input form="<?= $product->base->code_name . '_' . $variant->variant->code_suffix ?>" minlength="1" maxlength="255" type="text" name="variant_code_name" value="<?= $variant->variant->code_suffix ?>" placeholder="Variant id" required="required">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name . '_' . $variant->variant->code_suffix ?>" minlength="1" maxlength="255" type="text" name="variant_display_name" value="<?= $variant->variant->display_name ?>" placeholder="Display name" required="required">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name . '_' . $variant->variant->code_suffix ?>" type="color" name="variant_color" value="#<?= $variant->variant->color?>">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name . '_' . $variant->variant->code_suffix ?>" type="number" name="variant_ordinal" value="0" required="required">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name . '_' . $variant->variant->code_suffix ?>" step="0.01" type="number" name="variant_price" value="0">
                    </td>
                    <td></td>
                    <td></td>
                    <td>
                        <button form="<?= $product->base->code_name . '_' . $variant->variant->code_suffix ?>" type="submit" name="button_action" value="update_variant">Update</button>
                    </td>
                    <td>
                        <form id="<?= $product->base->code_name . '_' . $variant->variant->code_suffix ?>" method="POST">
                            <button form="<?= $product->base->code_name . '_' . $variant->variant->code_suffix ?>" type="submit" name="button_action" value="delete_variant">Delete</button>
                        </form>
                    </td>
                </tr>
<?php endforeach ?>
                <tr data-parent="<?= $product->base->code_name ?>">
                    <td>
                        <input form="<?= $product->base->code_name . '_new_variant' ?>" minlength="1" maxlength="255" type="text" name="variant_code_name" value="" placeholder="Variant id" required="required">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name . '_new_variant' ?>" minlength="1" maxlength="255" type="text" name="variant_display_name" value="" placeholder="Display name" required="required">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name . '_new_variant'?>" type="color" name="variant_color" value="#000000">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name . '_new_variant'?>" type="number" name="variant_ordinal" value="0" required="required">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name . '_new_variant' ?>" step="0.01" type="number" name="variant_price" value="0">
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <form id="<?= $product->base->code_name . '_new_variant' ?>" method="POST">
                            <button form="<?= $product->base->code_name . '_new_variant' ?>" type="submit" name="button_action" value="create_variant">Add</button>
                        </form>
                    </td>
                </tr>
<?php endforeach ?>
                <tr>
                    <td>
                        <input form="new_product" minlength="1" maxlength="255" type="text" name="base_code_name" placeholder="Product id" required="required">
                    </td>
                    <td>
                        <input form="new_product" minlength="1" maxlength="255" type="text" name="base_display_name" placeholder="Display name" required="required">
                    </td>
                    <td>
                        <select form="new_product" name="category">
<?php foreach ($categories as $category): ?>
                            <option value="<?= $category['display_name'] ?>"<?php if ($category['display_name'] == ($_POST['category'] ?? null)): ?> selected="selected"<?php endif ?>><?= $category['display_name'] ?></option>
<?php endforeach ?>
                        </select>
                    </td>
                    <td>
                        <textarea form="new_product" minlength="1" maxlength="255" name="short_description" placeholder="Product description" required="required"></textarea>
                    </td>
                    <td>
                        <input form="new_product" step="0.01" type="number" name="base_price" required="required">
                    </td>
                    <td>
                        <input form="new_product" type="checkbox" name="is_standalone">
                    </td>
                    <td></td>
                    <td></td>
                    <td>
                        <form id="new_product" method="POST">
                            <button form="new_product" type="submit" name="button_action" value="create_product">Add</button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </main>
