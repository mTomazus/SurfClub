<?php
$total = 0;
$item_count = 0;
foreach ($cart as $entry) {
    $item_count += is_array($entry) ? (int)($entry['qty'] ?? 0) : (int)$entry;
}
?>
<span id="drawer-cart-count" data-count="<?= $item_count ?>" hidden></span>

<div class="drawer-header">
    <span class="drawer-title">Krepšelis<?= $item_count > 0 ? " ($item_count)" : '' ?></span>
    <button class="drawer-close" onclick="closeCartDrawer()" aria-label="Uždaryti">&#215;</button>
</div>

<?php if (!empty($products)): ?>

<div class="drawer-items">
    <?php foreach ($products as $product):
        $entry = $cart[$product->id];
        $qty = is_array($entry) ? (int)($entry['qty'] ?? 0) : (int)$entry;
        $effective_price = $product->line_price ?? (($product->discount_price > 0) ? (float)$product->discount_price : (float)$product->price);
        $subtotal = $qty * $effective_price;
        $total += $subtotal;
    ?>
    <div class="drawer-item">
        <img src="<?= $product->picture_path ?>" alt="<?= out($product->name) ?>" class="drawer-item-img">
        <div class="drawer-item-info">
            <p class="drawer-item-name"><?= out($product->name) ?></p>
            <?php if (!empty($product->variant_label)): ?>
            <p class="drawer-item-variant" style="margin:0.1rem 0 0;font-size:0.72rem;color:var(--clr-dark,#555);opacity:0.75;"><?= out($product->variant_label) ?></p>
            <?php endif; ?>
            <p class="drawer-item-price">
                <?php if ($effective_price < (float)$product->price): ?>
                    <s><?= number_format($product->price, 2) ?></s> <?= number_format($effective_price, 2) ?> €
                <?php else: ?>
                    <?= number_format($effective_price, 2) ?> €
                <?php endif; ?>
            </p>
            <div class="drawer-qty-row">
                <div class="drawer-qty-controls">
                    <button class="cart-drawer-qty-btn" data-cart-action="decrease" data-product-id="<?= $product->id ?>">−</button>
                    <span><?= $qty ?></span>
                    <button class="cart-drawer-qty-btn" data-cart-action="increase" data-product-id="<?= $product->id ?>">+</button>
                </div>
                <button class="drawer-remove-btn" data-cart-remove="<?= $product->id ?>" aria-label="Trinti">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="drawer-footer">
    <div class="drawer-total">
        <span>Viso</span>
        <strong><?= number_format($total, 2) ?> €</strong>
    </div>
    <a href="products/checkout" class="drawer-checkout-btn">Užsakyti <i class="fa fa-arrow-right"></i></a>
    <button onclick="closeCartDrawer()" class="drawer-continue-btn">Tęsti apsipirkimą</button>
</div>

<?php else: ?>

<div class="drawer-empty">
    <i class="fa fa-shopping-basket"></i>
    <p>Krepšelis tuščias</p>
    <button onclick="closeCartDrawer()" class="drawer-continue-btn">Pradėti apsipirkimą</button>
</div>

<?php endif; ?>
