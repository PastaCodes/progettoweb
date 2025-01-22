<?php
require_once '../classes/Bundle.php';

$bundles = Bundle::fetch_bundles();
?>
        <template id="no-thumbnail">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                    <use href="assets/nothing.svg#nothing"></use>
                </svg>
                <p>No image available</p>
            </div>
        </template>
        <main>
            <section>
                <h1>Bundles</h1>
<?php foreach ($bundles as $bundle): ?>
                <div data-link="bundle?<?= $bundle->to_url_params() ?>" tabindex="0">
                    <p><?= $bundle->display_name ?></p>
<?php foreach ($bundle->products as $product): ?>
<?php if ($product->thumbnail() === null): ?>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                            <use href="assets/nothing.svg#nothing"></use>
                        </svg>
                        <p>No image available</p>
                    </div>
<?php else: ?>
                    <img src="<?= $product->thumbnail()->file ?>" loading="lazy" alt="<?= $product->thumbnail()->alt_text ?>">
<?php endif ?>
<?php endforeach ?>
<?php if ($bundle->variants_count > 1): ?>
                    <p>Available in <?= $bundle->variants_count ?> styles.</p>
<?php endif ?>
                </div>
<?php endforeach ?>
            </section>
        </main>
