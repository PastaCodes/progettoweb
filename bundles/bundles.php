<?php
require_once '../classes/Bundle.php';

$bundles = Bundle::fetch_bundles();
?>
        <template>
            <figure>
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                    <use href="assets/nothing.svg#nothing"></use>
                </svg>
                <figcaption>No image available</figcaption>
            </figure>
        </template>
        <main>
            <section>
                <h1>Bundles</h1>
                <ul>
<?php foreach ($bundles as $bundle): ?>
                    <li data-link="bundle?<?= $bundle->to_url_params() ?>" tabindex="0">
                        <p><?= $bundle->display_name ?></p>
<?php foreach ($bundle->products as $product): ?>
<?php if ($product->thumbnail() === null): ?>
                        <figure>
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" aria-label="">
                                <use href="assets/nothing.svg#nothing"></use>
                            </svg>
                            <figcaption>No image available</figcaption>
                        </figure>
<?php else: ?>
                        <img src="<?= $product->thumbnail()->file ?>" loading="lazy" alt="<?= $product->thumbnail()->alt_text ?>">
<?php endif ?>
<?php endforeach ?>
<?php if ($bundle->variants_count > 1): ?>
                        <p>Available in <?= $bundle->variants_count ?> styles.</p>
<?php endif ?>
                    </li>
<?php endforeach ?>
                </ul>
            </section>
        </main>
