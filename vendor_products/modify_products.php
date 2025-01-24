<?php
require_once '../classes/Category.php';
require_once '../classes/Product.php';

$button_action = null;
if (isset($_GET['button_action'])) {
    $button_action = $_GET['button_action'];
}
if ($button_action == 'update_product') {
    // Get the data from the form
    $product_id = $_GET['base_code_name'];
    $display_name = $_GET['base_display_name'];
    $category = $_GET['category'];
    $short_description = $_GET['short_description'];
    // Ensure the boolean is an integer
    $is_standalone = ($_GET['is_standalone'] ?? 0) ? 1 : 0;
    // Update that table's entry
    $database->update(
        table: 'product_base',
        data: [
            'code_name' => $product_id, 
            'display_name' => $display_name, 
            'category' => $category, 
            'short_description' => $short_description, 
            'standalone' => $is_standalone
        ],
        filters: ['product_base.code_name' => $product_id]
    );
} else if ($button_action == 'delete_product') {
    // Quick and easy query to delete a product from the db
    $product_id = $_GET['base_code_name'];
    $database->delete(
        table: 'product_base',
        filters: ['code_name' => $product_id]
    );
} else if ($button_action == 'update_variant') {
    // Get the data from the form
    
    // FIXME: Currently broken query, as it also needs the product_base_id
    $variant_id = $_GET['variant_code_name'];
    $display_name = $_GET['variant_display_name'];
    $color = substr($_GET['variant_color'], 1);
    $ordinal = $_GET['variant_ordinal'];
    $price = $_GET['variant_price'];
    // Update that table's entry
    $database->update(
        table: 'product_variant',
        data: [
            'code_suffix' => $variant_id,
            'ordinal' => $ordinal,
            'display_name' => $display_name,
            'color' => $color
        ],
        filters: ['product_variant.code_suffix' => $variant_id]
    );
    // Update the price from the info table
    $database->update(
        table: 'product_info',
        data: ['price' => $price],
        filters: ['variant' => $variant_id]
    );
} else if ($button_action == 'delete_variant') {
    // Quick and easy query to delete a product from the db
    
    // FIXME: Currently broken query, as it also needs the product_base_id
    $variant_id = $_GET['variant_code_name'];
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
                    <th scope="col">is_standalone / price</th>
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
                            <option value="<?= $category['display_name'] ?>"<?php if ($category['display_name'] == ($_GET['category'] ?? null)): ?> selected="selected"<?php endif ?>><?= $category['display_name'] ?></option>
<?php endforeach ?>
                        </select>
                    </td>
                    <td>
                        <textarea form="<?= $product->base->code_name ?>" minlength="1" maxlength="255" name="short_description" placeholder="Product description" required="required"></textarea>
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
                        <button form="<?= $product->base->code_name ?>" type="submit" name="button_action" value="update_product">Upd</button>
                    </td>
                    <td>
                        <form id="<?= $product->base->code_name ?>" action="vendor_products" method="GET">
                            <button form="<?= $product->base->code_name ?>" type="submit" name="button_action" value="delete_product">Del</button>
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
                        <input form="<?= $product->base->code_name . '_' . $variant->variant->code_suffix ?>" step="0.01" type="number" name="variant_price" value="0" required="required">
                    </td>
                    <td></td>
                    <td>
                        <button form="<?= $product->base->code_name . '_' . $variant->variant->code_suffix ?>" type="submit" name="button_action" value="update_variant">Upd</button>
                    </td>
                    <td>
                        <form id="<?= $product->base->code_name . '_' . $variant->variant->code_suffix ?>" action="vendor_products" method="GET">
                            <button form="<?= $product->base->code_name . '_' . $variant->variant->code_suffix ?>" type="submit" name="button_action" value="delete_variant">Del</button>
                        </form>
                    </td>
                </tr>
<?php endforeach ?>
<?php endforeach ?>
            </tbody>
        </table>
    </main>
