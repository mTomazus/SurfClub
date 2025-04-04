<div class="products-container">
    <h2>Perkamiausios prekės</h1>
    <div class="product-grid">
    <?php foreach ($products as $product) { ?>
        <form method="POST" action="<?= BASE_URL ?>products/add_to_cart" class="product-item">
            <input type="hidden" name="product_id" value="<?= $product->id ?>">
            <input type="hidden" name="quantity" value="1">

            <a href="products/item/<?= $product->id ?>">
                <img src="<?= $product->picture_path ?>" alt="<?= $product->name ?>" class="product-image">
                <div class="product-info">
                    <h3 class="product-name"><?= $product->name ?></h3>
                    <p class="product-price">€<?= number_format($product->price, 2) ?></p>
                </div>
            </a>
            <button type="submit" class="add-to-cart-btn">Į krepšelį</button>
        </form>
    <?php } ?>
    </div>
</div>