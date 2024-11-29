<?php
    require "Product.php";
    $products = [new Product('Braccialetto di perle', 10.00), new Product('Anello con serpente', 8.00), new Product('Collana con drago', 16.00), new Product('Anello con teschio', 9.00), new Product('Braccialetto di cuoio', 6.00)];
?>
        <main>
            <section>
                <?php foreach ($products as $product): ?>
                <article>
                    <section>
                        <?php if ($product->image): ?>
                        <img src="<?= $product->image ?>"/>
                        <?php else: ?>
                            <?php require "assets/ban-solid.svg" ?>
                            <p>No image available.</p>
                        <?php endif ?>
                    </section>
                    <section>
                        <p><?= $product->name ?></p>
                        <p>&euro; <?= number_format($product->price, 2, decimal_separator: ",", thousands_separator: ".") ?></p>
                    </section>
                    <button>Aggiungi al carrello</button>
                </article>
                <?php endforeach ?>
            </section>
        <main>