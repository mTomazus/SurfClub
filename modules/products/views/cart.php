<div class="cart-container" mx-get="products/cart" mx-select=".cart-container" mx-trigger="activate">
    <?php if (!empty($products)) { ?>
    <h2>Jūsų prekių krepšelis</h2>
        <table>
            <thead>
                <tr>
                    <th class="product-name">Prekė</th>
                    <th class="price">Kaina (€)</th>
                    <th class="quantity">Kiekis</th>
                    <th class="subtotal">Suma (€)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): 
                    $pid = $product->id;
                    $quantity = $cart[$pid];
                    $subtotal = $quantity * $product->price;
                    $total += $subtotal;
                ?>
                <tr>
                    <td><img src="<?= $product->picture_path ?>" alt="<?= $product->name ?>" class="product-small-image"><p><?= out($product->name) ?></p></td>
                    <td><?= number_format($product->price, 2) ?></td>
                    <td>
                        <div class="quantity-controls">
                            <a mx-post="products/update_cart" mx-vals='{"product_id": "<?= $product->id ?>", "action": "decrease"}' mx-target=".cart-container" mx-on-success=".cart-container"><i class="fa fa-minus" aria-hidden="true"></i></a>
                            <span class="qty-value"><?= $cart[$product->id] ?></span>
                            <a mx-post="products/update_cart" mx-vals='{"product_id": "<?= $product->id ?>", "action": "increase"}' mx-target=".cart-container" mx-on-success=".cart-container"><i class="fa fa-plus" aria-hidden="true"></i></a>
                        </div>
                        <a href="products/remove_from_cart/<?= $pid ?>" class="remove">Trinti<i class="fa fa-times" aria-hidden="true"></i></a>
                    </td>
                    <td><?= number_format($subtotal, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Viso:</strong></td>
                    <td><strong><?= number_format($total, 2) ?> €</strong></td>
                </tr>
            </tfoot>
        </table>
        <div class="cart-actions">
            <a href="products/checkout" class="checkout-btn">Užsakyti<i style="margin-left:0.5rem" class="fa fa-check" aria-hidden="true"></i></a>
            <a href="products" class="continue-shopping">Tęsti apsipirkimą</a>
        </div>
    </div>
    <?php } else {
        echo '<h2>Jūsų prekių krepšelis yra tuščias</h2>';
    } ?>

    <style>

/* Cart Container */
.cart-container {
    margin: 0 auto;
    padding: 20px;
}
.quantity, .price {
    width:120px;
}
.subtotal {
    width:160px;
}
.remove {
    color:red;
    text-decoration:none;
    & i {
        padding-left: 5px;
    }
}
.quantity-controls {
    display:flex;
    background: white;
    border:1px solid;
    align-items:center;
    text-align:center;
    justify-content:space-between;
    padding: 0 0.5rem;
    & i {
        padding:0.5rem;
    }
    & input {
        border:none;
        text-align:center;
    }
}
/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table th, table td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: center;
}

table th {
    background-color: transparent;
    color:var(--primary-darker);
}

table tfoot td {
    font-size: 1.2em;
}

.product-small-image {
    float:left;
    max-height:90px;
}

/* Buttons */
.update-btn, .checkout-btn, .remove-btn, .continue-shopping {
    display: inline-block;
    padding: 10px 20px;
    margin-top: 10px;
    text-decoration: none;
    color: #fff;
    border-radius: 5px;
    text-align: center;
}

.update-btn {
    background-color: #5bc0de;
}

.checkout-btn {
    background-color: #5cb85c;
}

.remove-btn {
    background-color: #d9534f;
}

.continue-shopping {
    background: none;
    margin: 0;
    color: var(--clr-primary);
}

.update-btn:hover, .checkout-btn:hover, .remove-btn:hover, .continue-shopping:hover {
    opacity: 0.9;
}

/* Form Actions */
.cart-actions {
    display: flex;
    flex-direction: column;
    float: right;
    margin-bottom:calc(65px + 1rem);
}
    </style>