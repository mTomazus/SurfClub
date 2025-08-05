<div class="product-page-container">
    <div class="product-page">
        <div class="product-small-image">
            <img src="<?= $products[0]->picture_path ?>" alt="<?= $products[0]->name ?>">
        </div>
        <div class="product-details">
            <h1><?= $products[0]->name ?></h1>
            <h3 class="product-price">€<?= number_format($products[0]->price, 2) ?></h3>
            <p class="product-price"><?= ucfirst($products[0]->option_name) ?>: <?= $products[0]->option_value ?></p>
            
            <p class="product-description"><?= nl2br($products[0]->description) ?></p>

            <form method="POST" action="<?= BASE_URL ?>products/add_to_cart">
                <input type="hidden" name="product_id" value="<?= $products[0]->id ?>">
                <label for="qty">Turime: <?= $products[0]->stock ?> vnt.</label>
                <label for="qty">Kiekis:</label>
                <input type="number" name="quantity" id="qty" value="1" min ="1" max="<?= $products[0]->stock ?>" class="qty-field">
                <button type="submit" class="add-cart-button">Į krepšelį <i class="fa fa-shopping-basket"></i></button>     
            </form>
        </div>
    </div>
</div>

<style>
    .product-page-container {
    padding: 40px 20px;
    max-width: 1100px;
    margin: auto;
}

.product-page {
    display: flex;
    flex-wrap: wrap;
    gap: 40px;
}

.product-small-image img {
    width: 100%;
    max-width: 500px;
    border-radius: 8px;
}

.product-details {
    flex: 1;
    & h3 {
        float: right;
        padding: 1rem;
        background: var(--border);
    }
}

.product-details h1 {
    font-size: 2em;
    margin-bottom: 10px;
}

.product-price {
    font-size: 1.4em;
    color: #222;
    margin-bottom: 20px;
}

.product-description {
    font-size: 1em;
    line-height: 1.5;
    margin-bottom: 25px;
}

.qty-field {
    width: 60px;
    padding: 8px;
    margin-right: 10px;
}

.add-cart-button {
    padding: 10px 25px;
    background-color: #000;
    color: #fff;
    border: none;
    cursor: pointer;
}

.add-cart-button:hover {
    background-color: #333;
}
</style>