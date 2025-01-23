<?php
require_once '../classes/Category.php';
require_once '../classes/Product.php';

$products = Product::fetch_products();
$categories = Category::fetch_all();

/* TODO:
 * Add current data to category, short_description and is_standalone (also price for variant)
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
                    <th scope="col">base_id</th>
                    <th scope="col">display_name</th>
                    <th scope="col">category</th>
                    <th scope="col">short_description</th>
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
                        <button data-show="<?= $product->base->code_name ?>">Variants</button>
<?php endif ?>
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name ?>" type="submit" value="Upd">
                    </td>
                    <td>
                        <form id="<?= $product->base->code_name ?>" action="vendor_products" method="POST">
                            <input type="submit" value="Del">
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
                        #<?= $variant->variant->color ?>
                    </td>
                    <td>
                        0
                    </td>
                    <td>
                        0.0 &euro;
                    </td>
                    <td></td>
                    <td>
                        <input form="<?= $product->base->code_name . '_' . $variant->variant->code_suffix ?>" type="submit" value="Upd">
                    </td>
                    <td>
                        <form id="<?= $product->base->code_name . '_' . $variant->variant->code_suffix ?>" action="vendor_products" method="POST">
                            <input type="submit" value="Del">
                        </form>
                    </td>
                </tr>
<?php endforeach ?>
<?php endforeach ?>
            </tbody>
        </table>
    </main>
