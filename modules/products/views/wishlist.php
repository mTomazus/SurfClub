<div class="wishlist-container">
    <h2>Mano norų sąrašas</h2>

    <?php if (empty($products)): ?>
        <p>Norų sąrašas tuščias.</p>
        <a href="products" class="continue-shopping">Tęsti apsipirkimą</a>
    <?php else: ?>
        <div class="wishlist-grid">
            <?php foreach ($products as $product):
                $picture_path = BASE_URL . 'products_module/images/products_pics/' . $product->id . '/' . $product->image;
            ?>
            <div class="wishlist-item">
                <a href="products/item/<?= $product->id ?>">
                    <img src="<?= $picture_path ?>" alt="<?= out($product->name) ?>">
                    <p><?= out($product->name) ?></p>
                    <p class="price"><?= number_format($product->price, 2) ?> €</p>
                </a>
                <a href="products/remove_from_wishlist/<?= $product->id ?>" class="remove-wishlist">Pašalinti</a>
            </div>
            <?php endforeach; ?>
        </div>
        <a href="products" class="continue-shopping">Tęsti apsipirkimą</a>
    <?php endif; ?>
</div>

<style>
.wishlist-container {
    margin: 0 auto;
    padding: 20px;
}
.wishlist-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
}
.wishlist-item {
    border: 1px solid #ddd;
    padding: 1rem;
    text-align: center;
    width: 200px;
}
.wishlist-item img {
    max-width: 100%;
    max-height: 200px;
    object-fit: cover;
}
.wishlist-item a {
    text-decoration: none;
    color: inherit;
    display: block;
}
.wishlist-item .price {
    font-weight: bold;
    color: var(--clr-primary, #333);
}
.remove-wishlist {
    color: red;
    font-size: 0.85rem;
    margin-top: 0.5rem;
    display: inline-block;
}
.continue-shopping {
    display: inline-block;
    margin-top: 1rem;
    text-decoration: none;
    color: var(--clr-primary);
}
</style>
