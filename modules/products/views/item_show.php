<?php
$p = $product;
$base_price   = ((float) $p->discount_price > 0) ? (float) $p->discount_price : (float) $p->price;
$has_variants = !empty($variants);

// Axes: option name => [distinct values, first-seen order]
$axes = [];
foreach ($variants as $v) {
    foreach ($v->options as $name => $val) {
        if (!isset($axes[$name])) {
            $axes[$name] = [];
        }
        if (!in_array($val, $axes[$name], true)) {
            $axes[$name][] = $val;
        }
    }
}

// Default selection: first in-stock variant, else first variant
$default = null;
foreach ($variants as $v) {
    if ((int) $v->stock > 0) { $default = $v; break; }
}
if (!$default && $has_variants) {
    $default = $variants[0];
}
$default_opts  = $default ? $default->options : [];
$default_vid   = $default ? (int) $default->id : '';
$display_price = $default ? (float) $default->effective_price : $base_price;

// Variant payload for client-side combo resolution
$variants_js = [];
foreach ($variants as $v) {
    $variants_js[] = [
        'id'    => (int) $v->id,
        'stock' => (int) $v->stock,
        'price' => round((float) $v->effective_price, 2),
        'opts'  => (object) $v->options,
    ];
}

$gallery = $gallery ?? [];
$thumbs  = array_merge([$p->picture_path], $gallery);
?>

<div class="item-wrap fade-up">

    <div class="item-gallery">
        <img src="<?= $p->picture_path ?>" alt="<?= out($p->name) ?>" class="item-img" id="item-main-img">

        <?php if (count($thumbs) > 1): ?>
        <div class="item-thumbs" id="item-thumbs">
            <?php foreach ($thumbs as $i => $thumb_src): ?>
            <button type="button" class="item-thumb<?= $i === 0 ? ' is-active' : '' ?>" data-src="<?= $thumb_src ?>" aria-label="<?= out($p->name) ?> nuotrauka <?= $i + 1 ?>">
                <img src="<?= $thumb_src ?>" alt="" loading="lazy">
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="item-details">

        <h1 class="item-title"><?= out($p->name) ?></h1>
        <p class="item-price" id="item-price">&euro;<?= number_format($display_price, 2) ?></p>

        <?php if (!empty($p->short_desc)): ?>
        <p class="item-short-desc"><?= out($p->short_desc) ?></p>
        <?php endif; ?>

        <hr class="item-rule">

        <?php foreach ($axes as $axis_name => $values): ?>
        <div class="variant-group">
            <span class="variant-label"><?= ucfirst(out($axis_name)) ?></span>
            <div class="variant-chips" data-axis="<?= out($axis_name) ?>">
                <?php foreach ($values as $val):
                    $checked = isset($default_opts[$axis_name]) && $default_opts[$axis_name] === $val;
                ?>
                <label class="variant-chip">
                    <input type="radio" name="axis_<?= out($axis_name) ?>" value="<?= out($val) ?>" <?= $checked ? 'checked' : '' ?>>
                    <?= out($val) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <p class="item-availability" id="item-availability"></p>

        <form method="POST" action="<?= BASE_URL ?>products/add_to_cart" class="item-form">
            <input type="hidden" name="product_id" value="<?= $p->id ?>">
            <input type="hidden" name="variant_id" id="selected-variant-id" value="<?= $default_vid ?>">

            <div class="quantity-controls">
                <button type="button" aria-label="Mažiau"
                    onclick="const i=this.nextElementSibling;i.value=Math.max(1,+i.value-1)"><i class="fa fa-minus" aria-hidden="true"></i></button>
                <input type="number" name="quantity" value="1" min="1" aria-label="Kiekis">
                <button type="button" aria-label="Daugiau"
                    onclick="this.previousElementSibling.value=+this.previousElementSibling.value+1"><i class="fa fa-plus" aria-hidden="true"></i></button>
            </div>

            <button type="submit" class="btn-add-cart" id="btn-add-cart">
                <span>Į krepšelį</span>
                <i class="fa fa-shopping-basket" aria-hidden="true"></i>
            </button>
        </form>

        <?php if (!empty($p->description)): ?>
        <details class="item-desc">
            <summary class="item-desc-summary">Aprašymas</summary>
            <p class="item-desc-body"><?= nl2br(out($p->description)) ?></p>
        </details>
        <?php endif; ?>

    </div>
</div>

<style>
/* ── Item page ────────────────────────────── */
.item-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 3rem;
    max-width: 1100px;
    margin: 2rem auto 4rem;
    padding: 0 1.5rem;
    align-items: flex-start;
}

.item-gallery {
    flex: 1 1 300px;
    max-width: 500px;
    position: sticky;
    top: 80px;
}

.item-img {
    width: 100%;
    display: block;
    border-radius: 2px;
    box-shadow: var(--shadow-md);
}

/* Thumbnail strip */
.item-thumbs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.item-thumb {
    width: 64px;
    height: 64px;
    padding: 0;
    border: 1.5px solid hsl(220 14% 86%);
    border-radius: 2px;
    background: white;
    cursor: pointer;
    overflow: hidden;
    transition:
        border-color var(--dur-micro) var(--ease-out),
        opacity var(--dur-micro) var(--ease-out);
}

.item-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.item-thumb:hover { border-color: var(--primary-darker); }

.item-thumb.is-active {
    border-color: var(--primary-darker);
}

.item-thumb:focus-visible {
    outline: none;
    box-shadow: var(--ring);
}

.item-details {
    flex: 1 1 280px;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.item-title {
    font-size: clamp(1.5rem, 4vw, 2.4rem);
    font-weight: 400;
    letter-spacing: 0.02em;
    line-height: 1.15;
    color: var(--primary-darker);
    margin: 0;
}

.item-price {
    font-size: 1.65rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    color: var(--primary-darker);
    margin: 0;
}

.item-short-desc {
    font-size: 0.925rem;
    color: var(--clr-dark);
    line-height: 1.65;
    margin: 0;
}

.item-rule {
    border: none;
    border-top: 1px solid hsl(220 14% 91%);
    margin: 0;
}

/* Variants */
.variant-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.variant-label {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: var(--clr-dark);
}

.variant-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.variant-chip {
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.9rem;
    border: 1.5px solid hsl(220 14% 86%);
    border-radius: 2px;
    cursor: pointer;
    font-size: 0.85rem;
    color: var(--primary-darker);
    user-select: none;
    transition:
        border-color var(--dur-micro) var(--ease-out),
        background var(--dur-micro) var(--ease-out),
        color var(--dur-micro) var(--ease-out);
    & input[type="radio"] { display: none; }
}

.variant-chip:hover {
    border-color: var(--primary-darker);
}

.variant-chip:has(input:checked) {
    border-color: var(--primary-darker);
    background: var(--primary-darker);
    color: hsl(0 0% 98%);
}

.item-availability {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--clr-dark);
    margin: 0;
    min-height: 1em;
}

/* Qty + cart form */
.item-form {
    display: flex;
    gap: 0.75rem;
    align-items: stretch;
}

.item-form .quantity-controls {
    display: flex;
    max-height: 40px;
    box-sizing: border-box;
    background: white;
    border: 1px solid;
    align-items: center;
    text-align: center;
    justify-content: space-between;
    padding: 0 0.5rem;
}
.item-form .quantity-controls button {
    margin: auto;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    color: inherit;
    font-family: inherit;
}
input[type="number"]::-webkit-inner-spin-button {
  display: none;
}
.item-form .quantity-controls i { padding: 0.5rem; }
.item-form .quantity-controls input {
    border: none;
    padding: 0;
    text-align: center;
    width: 40px;
    font-family: inherit;
    font-size: inherit;
}
.item-form .quantity-controls input:focus { outline: none; }

.btn-add-cart {
    flex: 1;
    margin: auto;
    box-sizing: border-box;
    max-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    padding: 0.75rem 1.5rem;
    background: var(--primary-darker);
    color: hsl(0 0% 98%);
    border: none;
    font-size: 0.8rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    cursor: pointer;
    font-family: inherit;
    border-radius: 2px;
    position: relative;
    overflow: hidden;
    transition:
        background var(--dur-micro) var(--ease-out),
        transform var(--dur-micro) var(--ease-out),
        box-shadow var(--dur-micro) var(--ease-out);
}

.btn-add-cart::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(105deg, transparent 40%, hsl(0 0% 100% / .12) 50%, transparent 60%);
    transform: translateX(-100%);
    transition: transform 0.55s var(--ease-out);
}

.btn-add-cart:hover {
    background: var(--primary);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}
.btn-add-cart:hover::after { transform: translateX(100%); }

.btn-add-cart:active {
    transform: translateY(0) scale(0.98);
    box-shadow: var(--shadow-xs);
    transition-duration: 80ms;
}

.btn-add-cart:focus-visible {
    outline: none;
    box-shadow: var(--ring), var(--shadow-sm);
}

.btn-add-cart:disabled {
    opacity: 0.45;
    cursor: not-allowed;
    background: var(--primary-darker);
    transform: none;
    box-shadow: none;
}
.btn-add-cart:disabled::after { display: none; }

/* Description accordion */
.item-desc {
    border-top: 1px solid hsl(220 14% 91%);
    padding-top: 0.75rem;
}

.item-desc-summary {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: var(--clr-dark);
    cursor: pointer;
    list-style: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    &::-webkit-details-marker { display: none; }
    &::after {
        content: '+';
        font-size: 1.1rem;
        font-weight: 300;
        transition: transform var(--dur-std) var(--ease-out);
    }
}

.item-desc[open] .item-desc-summary::after {
    transform: rotate(45deg);
}

.item-desc-body {
    font-size: 0.9rem;
    line-height: 1.7;
    color: var(--clr-dark);
    margin: 0.75rem 0 0;
}

/* Entrance animation */
.fade-up {
    opacity: 0;
    transform: translateY(20px);
    transition:
        opacity var(--dur-emphasis) var(--ease-out),
        transform var(--dur-emphasis) var(--ease-out);
}
.fade-up.is-visible {
    opacity: 1;
    transform: translateY(0);
}

@media (max-width: 600px) {
    .item-gallery { position: static; }
    .item-form { flex-direction: column; }
    .qty-stepper { justify-content: center; }
}

@media (prefers-reduced-motion: reduce) {
    .fade-up { opacity: 1; transform: none; transition: none; }
}
</style>

<script>
(function () {
    const el = document.querySelector('.fade-up');
    if (el) requestAnimationFrame(() => el.classList.add('is-visible'));

    // ── Multi-axis variant resolution ──────────────────────────
    const VARIANTS = <?= json_encode($variants_js, JSON_UNESCAPED_UNICODE) ?>;
    const hasVariants = VARIANTS.length > 0;
    const priceEl = document.getElementById('item-price');
    const availEl = document.getElementById('item-availability');
    const vidInput = document.getElementById('selected-variant-id');
    const addBtn = document.getElementById('btn-add-cart');
    const axisWraps = Array.from(document.querySelectorAll('.variant-chips[data-axis]'));
    const axisNames = axisWraps.map(w => w.dataset.axis);
    const fmt = n => '€' + Number(n).toFixed(2);

    function selected() {
        const sel = {};
        axisWraps.forEach(w => {
            const r = w.querySelector('input:checked');
            if (r) sel[w.dataset.axis] = r.value;
        });
        return sel;
    }

    function matchVariant(sel) {
        return VARIANTS.find(v => axisNames.every(a => v.opts[a] === sel[a]));
    }

    function update() {
        if (!hasVariants) return;
        const sel = selected();
        if (!axisNames.every(a => sel[a] !== undefined)) {
            vidInput.value = ''; addBtn.disabled = true;
            availEl.textContent = 'Pasirinkite variantą';
            return;
        }
        const v = matchVariant(sel);
        if (!v) {
            vidInput.value = ''; addBtn.disabled = true;
            availEl.textContent = 'Šis derinys neprieinamas';
            return;
        }
        priceEl.textContent = fmt(v.price);
        vidInput.value = v.id;
        if (v.stock > 0) {
            addBtn.disabled = false;
            availEl.textContent = v.stock <= 5 ? ('Liko: ' + v.stock + ' vnt.') : '';
        } else {
            addBtn.disabled = true;
            availEl.textContent = 'Išparduota';
        }
    }

    axisWraps.forEach(w => w.addEventListener('change', update));
    update();

    // ── Thumbnail swap ─────────────────────────────────────────
    const thumbs = document.getElementById('item-thumbs');
    const mainImg = document.getElementById('item-main-img');
    if (thumbs && mainImg) {
        thumbs.addEventListener('click', function (e) {
            const btn = e.target.closest('.item-thumb');
            if (!btn) return;
            mainImg.src = btn.dataset.src;
            thumbs.querySelectorAll('.item-thumb').forEach(t => t.classList.remove('is-active'));
            btn.classList.add('is-active');
        });
    }
})();
</script>
