<div class="product-page-container">
    <div class="product-page">
        <div class="product-small-image">
            <img src="<?= $product->picture_path ?>" alt="<?= $product->name ?>">
        </div>
        <div class="product-details">
            <h1><?= $product->name ?></h1>
            <p class="product-price">€<?= number_format($product->price, 2) ?></p>
            <p class="product-description"><?= nl2br($product->description) ?></p>

            <form method="POST" action="<?= BASE_URL ?>products/add_to_cart">
                <input type="hidden" name="product_id" value="<?= $product->id ?>">
                <label for="qty">Kiekis:</label>
                <input type="number" name="quantity" id="qty" value="1" min="1" class="qty-field">

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