<?php
require_once '../util/db.php';
require_once '../classes/Product.php';
require_once '../classes/ProductVariant.php';
require_once '../util/format.php';
require_once '../util/files.php';

$product = Product::from($_GET['id'], $_GET['variant'] ?? null);
$product->fetch_all_details();
?>
        <template id="no-thumbnail">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-label="No image available">
                    <use href="assets/nothing.svg#root"></use>
                </svg>
                <p>No image available</p>
            </div>
        </template>
        <main>
<?php if ($product === null): ?>
            <h1>Product not found</h1>
<?php else: ?>
<?php if ($product->thumbnail() === null): ?>
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-label="No image available">
                    <use href="assets/nothing.svg#root"></use>
                </svg>
                <span>No image available</span>
            </div>
<?php else: ?>
            <img src="<?= $product->thumbnail()->file ?>" loading="lazy" alt="<?= $product->thumbnail()->alt_text ?>">
<?php endif ?>
<?php if (!empty($product->base->variants)): ?>
            <div>
<?php foreach ($product->base->variants as $variant): ?>
                <input type="radio" name="variant" <?= $variant->to_radio_attributes(selected_suffix: $product->variant->code_suffix) ?>>
<?php endforeach ?>
            </div>
<?php endif ?>
            <h1><?= $product->base->display_name ?></h1>
            <p><?= $product->base->short_description ?></p>
<?php if ($product->base->is_standalone): ?>
            <button>Add to cart</button>
<?php endif ?>
<?php endif ?>
        </main>
