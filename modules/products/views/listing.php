<div class="products-container">
    <h2><?= $page_title ?></h2>
    <div class="product-grid">
    <?php foreach ($products as $product):
        $oos = isset($product->is_available) && !$product->is_available;
        $has_discount = $product->discount_price > 0;
    ?>
        <form method="POST" action="<?= BASE_URL ?>products/add_to_cart" class="product-item<?= $oos ? ' is-oos' : '' ?>">
            <input type="hidden" name="product_id" value="<?= $product->id ?>">
            <input type="hidden" name="quantity" value="1">

            <a href="products/item/<?= $product->id ?>">
                <span class="product-image-wrap">
                    <img src="<?= $product->picture_path ?>" alt="<?= out($product->name) ?>" class="product-image">
                    <?php if ($oos): ?><span class="product-oos-badge">Išparduota</span><?php endif; ?>
                </span>
                <div class="product-info">
                    <h3 class="product-name"><?= out($product->name) ?></h3>
                    <p class="product-price">
                        <?php if ($has_discount): ?>
                            <s>&euro;<?= number_format($product->price, 2) ?></s> <span class="product-price-now">&euro;<?= number_format($product->discount_price, 2) ?></span>
                        <?php else: ?>
                            &euro;<?= number_format($product->price, 2) ?>
                        <?php endif; ?>
                    </p>
                </div>
            </a>

            <?php if ($oos): ?>
                <button type="button" class="add-to-cart-btn is-disabled" disabled>Išparduota</button>
            <?php else: ?>
                <button type="submit" class="add-to-cart-btn">Į krepšelį</button>
            <?php endif; ?>
        </form>
    <?php endforeach; ?>
    </div>
</div>

<style>
.product-item { position: relative; }
.product-image-wrap { position: relative; display: block; }
.product-oos-badge {
    position: absolute;
    top: 0.6rem;
    left: 0.6rem;
    z-index: 2;
    padding: 0.25rem 0.6rem;
    background: hsl(0 0% 10% / 0.82);
    color: hsl(0 0% 98%);
    font-size: 0.62rem;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    border-radius: 2px;
}
.product-item.is-oos .product-image { opacity: 0.5; filter: grayscale(0.4); }
.product-price s { color: var(--clr-dark); font-weight: 400; opacity: 0.65; margin-right: 0.25rem; }
.product-price-now { color: var(--primary-darker); }
.add-to-cart-btn.is-disabled {
    opacity: 1;
    transform: none;
    background: hsl(220 9% 60%);
    cursor: not-allowed;
}
.add-to-cart-btn.is-disabled:hover { background: hsl(220 9% 60%); }
</style>
