<?php
// Check if vendor
if (!isset($_SESSION['vendor'])) {
    header('Location: ../shop');
    exit();
}

require_once '../classes/Product.php';
require_once '../classes/Category.php';

$product_base_id = null;
$button_action = null;
if (isset($_POST['button_action'])) {
    $params = explode(':', $_POST['button_action']);
    $button_action = $params[0];
    if (isset($params[1])) {
        $product_base_id = $params[1];
    }
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
            filters: [
                'code_suffix' => $variant_id, 
                'base' => $product_base_id
            ]
        );
    }
} else if ($button_action == 'delete_variant') {
    // Quick and easy query to delete a product from the db
    $variant_id = $_POST['variant_code_name'];
    $database->delete(
        table: 'product_variant',
        filters: [
            'code_suffix' => $variant_id, 
            'base' => $product_base_id
        ]
    );
}

$products_result = $database->find(
    table: 'product_base',
    joins: [
        [
            'type' => 'LEFT',
            'table' => 'product_variant',
            'on' => 'product_variant.base = code_name',
        ],
    ],
    options: [
        'order_by' => [
            'code_name' => 'ASC',
            'ordinal' => 'ASC'
        ]
    ]
);
$products = [];
foreach ($products_result as $products_row) {
    if (!array_key_exists($products_row['code_name'], $products)) {
        $product = $products[$products_row['code_name']] = Product::from($products_row['code_name'], $products_row['code_suffix']);
        $product->base->display_name = $products_row['product_base.display_name'];
        $product->base->short_description = $products_row['short_description'];
        $product->base->is_standalone = $products_row['standalone'];
        $product->base->category = $products_row['category'];
        $product->price = $products_row['price_base'];
        $product->base->variants = [];
    } else {
        $product = $products[$products_row['code_name']];
    }
    if (!$products_row['code_suffix']) {
        continue;
    }
    $variant_product = new Product($product->base, new ProductVariant($products_row['code_suffix']));
    $variant_product->variant->display_name = $products_row['product_variant.display_name'];
    $variant_product->variant->color = $products_row['color'];
    $variant_product->variant->ordinal = $products_row['ordinal'];
    $variant_product->price = $products_row['price_override'] ?? false;
    $product->base->variants[] = $variant_product;
}
$categories = Category::fetch_all();
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
                            <option value="<?= $category['code_name'] ?>" <?php if ($product->base->category === $category['code_name']): ?> selected="selected"<?php endif ?>><?= $category['display_name'] ?></option>
<?php endforeach ?>
                        </select>
                    </td>
                    <td>
                        <textarea form="<?= $product->base->code_name ?>" minlength="1" maxlength="255" name="short_description" placeholder="Product description" required="required"><?= $product->base->short_description ?></textarea>
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name ?>" step="0.01" type="number" name="base_price" value="<?= number_format($product->price, 2, thousands_separator: '') ?>" required="required">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name ?>" type="checkbox" name="is_standalone"<?php if ($product->base->is_standalone): ?> checked="checked"<?php endif ?>>
                    </td>
                    <td>
<?php if ($product->base->variants): ?>
                        <button data-show="<?= $product->base->code_name ?>">&#9660;</button>
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
                        <input form="<?= $product->base->code_name . '-' . $variant->variant->code_suffix ?>" minlength="1" maxlength="255" type="text" name="variant_code_name" value="<?= $variant->variant->code_suffix ?>" placeholder="Variant id" required="required">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name . '-' . $variant->variant->code_suffix ?>" minlength="1" maxlength="255" type="text" name="variant_display_name" value="<?= $variant->variant->display_name ?>" placeholder="Display name" required="required">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name . '-' . $variant->variant->code_suffix ?>" type="color" name="variant_color" value="#<?= $variant->variant->color?>">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name . '-' . $variant->variant->code_suffix ?>" type="number" name="variant_ordinal" value="<?= $variant->variant->ordinal ?>" required="required">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name . '-' . $variant->variant->code_suffix ?>" step="0.01" type="number" name="variant_price"<?php if ($variant->price !== false): ?> value="<?= number_format($variant->price, 2, thousands_separator: '') ?>"<?php endif ?>>
                    </td>
                    <td colspan="2"></td>
                    <td>
                        <button form="<?= $product->base->code_name . '-' . $variant->variant->code_suffix ?>" type="submit" name="button_action" value="update_variant:<?= $product->base->code_name ?>">Update</button>
                    </td>
                    <td>
                        <form id="<?= $product->base->code_name . '-' . $variant->variant->code_suffix ?>" method="POST">
                            <button form="<?= $product->base->code_name . '-' . $variant->variant->code_suffix ?>" type="submit" name="button_action" value="delete_variant:<?= $product->base->code_name ?>">Delete</button>
                        </form>
                    </td>
                </tr>
<?php endforeach ?>
                <tr data-parent="<?= $product->base->code_name ?>">
                    <td>
                        <input form="<?= $product->base->code_name . '-new-variant' ?>" minlength="1" maxlength="255" type="text" name="variant_code_name" value="" placeholder="Variant id" required="required">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name . '-new-variant' ?>" minlength="1" maxlength="255" type="text" name="variant_display_name" value="" placeholder="Display name" required="required">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name . '-new-variant'?>" type="color" name="variant_color">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name . '-new-variant'?>" type="number" name="variant_ordinal" required="required">
                    </td>
                    <td>
                        <input form="<?= $product->base->code_name . '-new-variant' ?>" step="0.01" type="number" name="variant_price">
                    </td>
                    <td></td>
                    <td colspan="3">
                        <form id="<?= $product->base->code_name . '-new-variant' ?>" method="POST">
                            <button form="<?= $product->base->code_name . '-new-variant' ?>" type="submit" name="button_action" value="create_variant">Add</button>
                        </form>
                    </td>
                </tr>
<?php endforeach ?>
                <tr>
                    <td>
                        <input form="new-product" minlength="1" maxlength="255" type="text" name="base_code_name" placeholder="Product id" required="required">
                    </td>
                    <td>
                        <input form="new-product" minlength="1" maxlength="255" type="text" name="base_display_name" placeholder="Display name" required="required">
                    </td>
                    <td>
                        <select form="new-product" name="category">
<?php foreach ($categories as $category): ?>
                            <option value="<?= $category['code_name'] ?>"<?php if ($category['code_name'] == ($_POST['category'] ?? null)): ?> selected="selected"<?php endif ?>><?= $category['display_name'] ?></option>
<?php endforeach ?>
                        </select>
                    </td>
                    <td>
                        <textarea form="new-product" minlength="1" maxlength="255" name="short_description" placeholder="Product description" required="required"></textarea>
                    </td>
                    <td>
                        <input form="new-product" step="0.01" type="number" name="base_price" required="required">
                    </td>
                    <td>
                        <input form="new-product" type="checkbox" name="is_standalone">
                    </td>
                    <td colspan="3">
                        <form id="new-product" method="POST">
                            <button form="new-product" type="submit" name="button_action" value="create_product">Add</button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </main>
