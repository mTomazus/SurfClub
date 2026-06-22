<?php
/**
 * Products admin — create / edit form (admin_area template).
 * Self-contained styling matching the manage + show panels (charcoal + cyan).
 *
 * Form contract is unchanged: posts name, description, short_desc, price,
 * discount_price, in_stock, status, categories[], variants[], image (hidden)
 * and submit=Submit to $form_location (products/submit/{id}).
 */
$selected_ids = array_map('intval', $selected_categories);
$status_val = $status ?? 'active';
?>
<div class="pr">

<style>
@import url('https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600;700&display=swap');

:root {
  --pr-bg:        #15141a;
  --pr-surface:   #1c1b23;
  --pr-surface-2: #211f2a;
  --pr-line:      rgba(255,255,255,0.06);
  --pr-line-2:    rgba(255,255,255,0.11);
  --pr-text:      #f4f3f8;
  --pr-muted:     #a09db1;
  --pr-faint:     #6c6979;
  --pr-accent:    #45c4d6;
  --pr-green:     #2ec27e;
  --pr-red:       #e5484d;
  --pr-font: 'Geist', 'Segoe UI', system-ui, -apple-system, sans-serif;
  --pr-mono: 'Geist Mono', 'SF Mono', ui-monospace, Menlo, monospace;
  --pr-ease: cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes pr-fade { from { opacity: 0; } }

.pr {
  margin: 0.9rem;
  padding: clamp(1.25rem, 2vw, 1.85rem);
  background: var(--pr-bg);
  border: 1px solid var(--pr-line);
  border-radius: 16px;
  color: var(--pr-text);
  font-family: var(--pr-font);
  animation: pr-fade 0.4s var(--pr-ease) both;
}

/* ── Header ──────────────────────────────────────────────── */
.pr-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.1rem;
}
.pr-eyebrow {
  margin: 0 0 0.45rem;
  font-family: var(--pr-mono);
  font-size: 0.62rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  color: var(--pr-accent);
}
.pr-title {
  margin: 0;
  font-size: clamp(1.3rem, 2.4vw, 1.7rem);
  font-weight: 700;
  line-height: 1.1;
  letter-spacing: -0.03em;
  color: var(--pr-text);
}
.pr-actions { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }

/* ── Buttons (beat the theme's bare button rule) ─────────── */
.pr-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  height: 38px;
  padding: 0 1rem !important;
  margin: 0 !important;
  border-radius: 9px;
  font-family: var(--pr-font) !important;
  font-size: 0.8rem;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  box-shadow: none;
  transition: background 0.15s var(--pr-ease), color 0.15s var(--pr-ease),
              border-color 0.15s var(--pr-ease), transform 0.12s var(--pr-ease),
              filter 0.15s var(--pr-ease);
}
.pr-btn:active { transform: translateY(1px) scale(0.985); }
.pr-btn--ghost { background: transparent !important; border: 1px solid var(--pr-line-2) !important; color: var(--pr-muted) !important; }
.pr-btn--ghost:hover { background: rgba(255,255,255,0.05) !important; color: var(--pr-text) !important; }
.pr-btn--accent { background: var(--pr-accent) !important; border: 1px solid var(--pr-accent) !important; color: #08252a !important; font-weight: 700; }
.pr-btn--accent:hover { filter: brightness(1.08); color: #08252a !important; }
.pr-add { height: 34px; font-size: 0.76rem; margin-top: 0.6rem !important; }

/* ── Validation errors ───────────────────────────────────── */
.pr-errors {
  margin: 0 0 1rem;
  padding: 0.7rem 0.9rem;
  background: rgba(229,72,77,0.1);
  border: 1px solid rgba(229,72,77,0.4);
  border-radius: 10px;
  color: #ffb3b5;
  font-size: 0.8rem;
}
.pr-errors p, .pr-errors ul, .pr-errors li { margin: 0.15rem 0; color: inherit; text-align: left; }
.pr-field-err { margin: 0 0 0.5rem; color: var(--pr-red); font-size: 0.74rem; }
.pr-field-err p { margin: 0; color: inherit; text-align: left; }

/* ── Form layout (reset the theme's `form { display:grid }`) ─ */
.pr-form { display: block !important; width: 100%; }
.pr-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  align-items: start;
}
.pr-span-2 { grid-column: 1 / -1; }

.pr-panel {
  border: 1px solid var(--pr-line-2);
  border-radius: 12px;
  background: var(--pr-surface);
  overflow: hidden;
}
.pr-panel__head {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.62rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--pr-faint);
  background: var(--pr-surface-2);
  padding: 0.6rem 0.9rem;
}
.pr-opt {
  font-family: var(--pr-mono);
  font-size: 0.58rem;
  letter-spacing: 0.06em;
  color: var(--pr-faint);
  border: 1px solid var(--pr-line-2);
  border-radius: 99px;
  padding: 0.05rem 0.4rem;
  text-transform: none;
}
.pr-panel__body { padding: 0.9rem; display: flex; flex-direction: column; gap: 0.85rem; }

/* Fields */
.pr-field { display: flex; flex-direction: column; gap: 0.35rem; }
.pr-field label {
  margin: 0;
  font-size: 0.66rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--pr-muted);
  text-align: left;
}
.pr-two { display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem; }

/* Inputs (override the theme's bare input/select rules) */
.pr-form input[type="text"],
.pr-form input[type="number"],
.pr-form textarea,
.pr-form select,
.pr-form .pr-input {
  width: 100% !important;
  height: auto;
  margin: 0 !important;
  padding: 0.55rem 0.7rem;
  border: 1px solid var(--pr-line-2);
  border-radius: 9px;
  background: var(--pr-bg);
  color: var(--pr-text) !important;
  font-family: var(--pr-font) !important;
  font-size: 0.85rem;
  text-align: left !important;
  box-sizing: border-box;
  box-shadow: none;
  outline: none;
  transition: border-color 0.15s var(--pr-ease), box-shadow 0.15s var(--pr-ease);
}
.pr-form input[type="number"] { height: 40px; }
.pr-form select { height: 40px; appearance: auto; }
.pr-form textarea { min-height: 90px; resize: vertical; line-height: 1.5; }
.pr-form input::placeholder, .pr-form textarea::placeholder { color: var(--pr-faint); }
.pr-form input:focus, .pr-form textarea:focus, .pr-form select:focus, .pr-form .pr-input:focus {
  border-color: var(--pr-accent);
  box-shadow: 0 0 0 3px rgba(69,196,214,0.18);
}

/* Category checkboxes */
.pr-checks { display: flex; flex-wrap: wrap; gap: 0.5rem; }
.pr-check {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.4rem 0.75rem;
  border: 1px solid var(--pr-line-2);
  border-radius: 9px;
  background: var(--pr-bg);
  cursor: pointer;
  font-size: 0.8rem;
  color: var(--pr-text);
  text-transform: capitalize;
  user-select: none;
  transition: border-color 0.15s var(--pr-ease), background 0.15s var(--pr-ease);
}
.pr-check:hover { border-color: rgba(69,196,214,0.5); }
.pr-check:has(input:checked) { border-color: var(--pr-accent); background: rgba(69,196,214,0.1); }
.pr-check input[type="checkbox"] {
  width: auto !important;
  height: auto !important;
  margin: 0 !important;
  accent-color: var(--pr-accent);
  cursor: pointer;
}

/* Variants */
.pr-variants { display: flex; flex-direction: column; gap: 0.5rem; }
.pr-variant-head,
.pr-variant-row {
  display: grid;
  grid-template-columns: 1fr 84px 96px 120px 34px;
  gap: 0.5rem;
  align-items: center;
}
.pr-variant-head {
  padding: 0 0.1rem;
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--pr-faint);
}
.pr-variant-del {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 34px !important;
  height: 34px;
  padding: 0 !important;
  margin: 0 !important;
  border: 1px solid var(--pr-line-2) !important;
  border-radius: 9px;
  background: transparent !important;
  color: var(--pr-muted) !important;
  font-family: var(--pr-font) !important;
  font-size: 1.1rem;
  line-height: 1;
  cursor: pointer;
  transition: background 0.15s var(--pr-ease), color 0.15s var(--pr-ease), border-color 0.15s var(--pr-ease);
}
.pr-variant-del:hover { background: rgba(229,72,77,0.15) !important; color: var(--pr-red) !important; border-color: rgba(229,72,77,0.45) !important; }

.pr-hint { margin: 0; font-size: 0.74rem; color: var(--pr-faint); line-height: 1.5; }
.pr-hint code {
  font-family: var(--pr-mono);
  font-size: 0.72rem;
  color: var(--pr-accent);
  background: rgba(69,196,214,0.08);
  padding: 0.05rem 0.35rem;
  border-radius: 5px;
}

/* Footer */
.pr-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.6rem;
  margin-top: 1.1rem;
  padding-top: 1.1rem;
  border-top: 1px solid var(--pr-line);
}

@media (max-width: 56rem) { .pr-grid { grid-template-columns: 1fr; } }
@media (max-width: 40rem) {
  .pr { padding: 1rem; border-radius: 12px; }
  .pr-two { grid-template-columns: 1fr; }
  .pr-footer { flex-direction: column-reverse; }
  .pr-footer .pr-btn { justify-content: center; }
}
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
}
</style>

<div class="pr-head">
    <div>
        <p class="pr-eyebrow">Molas Surf Club &middot; Shop product</p>
        <h2 class="pr-title"><?= out($headline) ?></h2>
    </div>
    <div class="pr-actions">
        <?= anchor($cancel_url, '&larr; Cancel', ['class' => 'pr-btn pr-btn--ghost']) ?>
    </div>
</div>

<?php $all_errs = validation_errors(); if (!empty($all_errs)) echo '<div class="pr-errors">' . $all_errs . '</div>'; ?>

<?php echo form_open($form_location, ['class' => 'pr-form']); ?>

    <div class="pr-grid">

        <!-- Basics -->
        <section class="pr-panel pr-span-2">
            <div class="pr-panel__head">Basics</div>
            <div class="pr-panel__body">
                <div class="pr-field">
                    <label for="pr-name">Name</label>
                    <input type="text" id="pr-name" name="name" value="<?= out($name) ?>" placeholder="Enter product name" autocomplete="off">
                </div>
                <div class="pr-field">
                    <label for="pr-short">Short description</label>
                    <input type="text" id="pr-short" name="short_desc" value="<?= out($short_desc) ?>" placeholder="One-line summary shown on the product page" autocomplete="off">
                </div>
                <div class="pr-field">
                    <label for="pr-desc">Description</label>
                    <textarea id="pr-desc" name="description" placeholder="Full product description"><?= out($description) ?></textarea>
                </div>
            </div>
        </section>

        <!-- Pricing & inventory -->
        <section class="pr-panel">
            <div class="pr-panel__head">Pricing &amp; inventory</div>
            <div class="pr-panel__body">
                <div class="pr-two">
                    <div class="pr-field">
                        <label for="pr-price">Price (&euro;)</label>
                        <input type="number" id="pr-price" name="price" value="<?= out($price) ?>" placeholder="0.00" min="0" step="0.01">
                    </div>
                    <div class="pr-field">
                        <label for="pr-discount">Discount price (&euro;)</label>
                        <input type="number" id="pr-discount" name="discount_price" value="<?= out($discount_price) ?>" placeholder="0.00" min="0" step="0.01">
                    </div>
                    <div class="pr-field">
                        <label for="pr-stock">In stock (qty)</label>
                        <input type="number" id="pr-stock" name="in_stock" value="<?= out($in_stock) ?>" placeholder="e.g. 12" min="0" step="1">
                    </div>
                    <div class="pr-field">
                        <label for="pr-status">Status</label>
                        <select id="pr-status" name="status">
                            <?php foreach (['active' => 'Active', 'inactive' => 'Inactive', 'archived' => 'Archived'] as $val => $label): ?>
                            <option value="<?= $val ?>"<?= $status_val === $val ? ' selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <p class="pr-hint">Set a discount price above 0 to show a reduced price; leave at 0 for none.</p>
            </div>
        </section>

        <!-- Categories -->
        <section class="pr-panel">
            <div class="pr-panel__head">Categories</div>
            <div class="pr-panel__body">
                <?php $cat_err = validation_errors('categories'); if (!empty($cat_err)) echo '<div class="pr-field-err">' . $cat_err . '</div>'; ?>
                <div class="pr-checks">
                    <?php foreach ($category_options as $cat_id => $cat_name): ?>
                    <label class="pr-check">
                        <input type="checkbox" name="categories[]" value="<?= (int) $cat_id ?>"<?= in_array((int) $cat_id, $selected_ids, true) ? ' checked' : '' ?>>
                        <span><?= out($cat_name) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <p class="pr-hint">Pick at least one &mdash; categories control which shop listings the product appears in.</p>
            </div>
        </section>

        <!-- Variants -->
        <section class="pr-panel pr-span-2">
            <div class="pr-panel__head">Variants <span class="pr-opt">optional</span></div>
            <div class="pr-panel__body">
                <p class="pr-hint">One row per sellable combination (SKU). List its options as <code>name:value</code> pairs &mdash; e.g. <code>size:M, color:Black</code>. Each combo has its own stock; leave <strong>Price</strong> blank to inherit the product price, or set a per-combo price. Remove a row to delete that SKU.</p>
                <div id="variants-container" class="pr-variants">
                    <?php if (!empty($variants)): ?>
                        <div class="pr-variant-head">
                            <span>Options (name:value, …)</span><span>Stock</span><span>Price &euro;</span><span>SKU</span><span></span>
                        </div>
                        <?php $i = 0; foreach ($variants as $v): ?>
                        <div class="pr-variant-row">
                            <input type="hidden" name="variants[<?= $i ?>][id]" value="<?= (int) ($v->id ?? 0) ?>">
                            <input type="text" name="variants[<?= $i ?>][options]" class="pr-input" value="<?= out((string) ($v->options_str ?? '')) ?>" placeholder="size:M, color:Black">
                            <input type="number" name="variants[<?= $i ?>][stock]" class="pr-input" value="<?= out((string) ($v->stock ?? '')) ?>" min="0" step="1" placeholder="0">
                            <input type="number" name="variants[<?= $i ?>][price]" class="pr-input" value="<?= out($v->price !== null ? (string) $v->price : '') ?>" min="0" step="0.01" placeholder="base">
                            <input type="text" name="variants[<?= $i ?>][sku]" class="pr-input" value="<?= out((string) ($v->sku ?? '')) ?>" placeholder="optional">
                            <button type="button" class="pr-variant-del" onclick="removeVariant(this)" aria-label="Remove variant" title="Remove variant">&times;</button>
                        </div>
                        <?php $i++; endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="pr-btn pr-btn--ghost pr-add" onclick="addVariant()">+ Add variant</button>
            </div>
        </section>

    </div>

    <div class="pr-footer">
        <?php echo form_hidden('image', $image); ?>
        <?= anchor($cancel_url, 'Cancel', ['class' => 'pr-btn pr-btn--ghost']) ?>
        <button type="submit" name="submit" value="Submit" class="pr-btn pr-btn--accent">Save product</button>
    </div>

<?php echo form_close(); ?>

</div><!-- /.pr -->

<script>
let variantIndex = <?= count($variants) ?>;

function addVariant() {
    const c = document.getElementById("variants-container");

    if (!c.querySelector(".pr-variant-head")) {
        const head = document.createElement("div");
        head.className = "pr-variant-head";
        head.innerHTML = "<span>Options (name:value, …)</span><span>Stock</span><span>Price €</span><span>SKU</span><span></span>";
        c.appendChild(head);
    }

    const i = variantIndex++;
    const row = document.createElement("div");
    row.className = "pr-variant-row";
    row.innerHTML =
        '<input type="hidden" name="variants[' + i + '][id]" value="0">' +
        '<input type="text" name="variants[' + i + '][options]" class="pr-input" placeholder="size:M, color:Black">' +
        '<input type="number" name="variants[' + i + '][stock]" class="pr-input" min="0" step="1" placeholder="0">' +
        '<input type="number" name="variants[' + i + '][price]" class="pr-input" min="0" step="0.01" placeholder="base">' +
        '<input type="text" name="variants[' + i + '][sku]" class="pr-input" placeholder="optional">' +
        '<button type="button" class="pr-variant-del" onclick="removeVariant(this)" aria-label="Remove variant" title="Remove variant">&times;</button>';
    c.appendChild(row);
    row.querySelector('input[type="text"]').focus();
}

function removeVariant(btn) {
    const row = btn.closest(".pr-variant-row");
    if (row) row.remove();
}
</script>
