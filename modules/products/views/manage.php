<?php
/**
 * Products admin — manage panel.
 *
 * Loaded two ways:
 *  - via the admin dashboard (mx-get="products/manage" mx-select="main",
 *    mx-select-oob="#title:#top-title" — so the hidden #title block must stay)
 *  - direct load through the default_admin theme (products/manage URL)
 * The inline <style> keeps the panel self-contained in both contexts and
 * matches the charcoal + cyan design language of the other admin panels.
 */
?>
<div id="title" style="display:none"><h1><?= out($headline) ?></h1></div>

<div id="stat-panel">
    <div class="pr-stats">
        <div class="pr-stat pr-stat--lead">
            <span class="pr-stat__num"><?= (int) $pagination_data['total_rows'] ?></span>
            <span class="pr-stat__label">Products</span>
        </div>
    </div>
</div>

<div id="products-container">

<style>
/* ============================================================
   PRODUCTS ADMIN — charcoal base, single cyan accent
   (same design language as the other admin panels)
   ============================================================ */
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
  --pr-amber:     #e0a64b;
  --pr-red:       #e5484d;

  --pr-font: 'Geist', 'Segoe UI', system-ui, -apple-system, sans-serif;
  --pr-mono: 'Geist Mono', 'SF Mono', ui-monospace, Menlo, monospace;

  --pr-ease: cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes pr-fade { from { opacity: 0; } }

/* ── Stat strip ──────────────────────────────────────────── */
#stat-panel { margin: 0.9rem 0.9rem 1.1rem; font-family: var(--pr-font); color: var(--pr-text); }
.pr-stats {
  display: grid;
  grid-template-columns: 1fr;
  border: 1px solid var(--pr-line-2);
  border-radius: 14px;
  overflow: hidden;
  animation: pr-fade 0.4s var(--pr-ease) both;
}
.pr-stat { display: flex; flex-direction: column; gap: 0.3rem; padding: 0.95rem 1.15rem; background: var(--pr-surface); }
.pr-stat--lead { background: linear-gradient(135deg, rgba(69,196,214,0.09), rgba(69,196,214,0.02) 65%), var(--pr-surface); }
.pr-stat__num {
  font-family: var(--pr-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
  letter-spacing: -0.03em;
  color: var(--pr-accent);
}
.pr-stat__label {
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--pr-muted);
}

/* ── Shell ───────────────────────────────────────────────── */
.pr-shell {
  margin: 0 0.9rem 1.5rem;
  padding: clamp(1.25rem, 2vw, 1.85rem);
  background: var(--pr-bg);
  border: 1px solid var(--pr-line);
  border-radius: 16px;
  color: var(--pr-text);
  font-family: var(--pr-font);
}
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
  font-size: clamp(1.4rem, 2.5vw, 1.8rem);
  font-weight: 700;
  line-height: 1;
  letter-spacing: -0.03em;
  color: var(--pr-text);
  text-transform: none;
}
.pr-tools { display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap; }
.pr-tools form {
  display: flex;
  flex-direction: row;
  gap: 0.4rem;
  width: auto;
  align-items: center;
  grid-template-columns: unset;
  margin: 0;
}
.pr-tools input[type="search"] {
  width: 190px;
  height: 36px;
  margin: 0 !important;
  padding: 0 0.7rem;
  border: 1px solid var(--pr-line-2);
  border-radius: 9px;
  background: var(--pr-surface);
  color: var(--pr-text);
  font-family: var(--pr-font);
  font-size: 0.82rem;
  text-align: left;
  box-sizing: border-box;
  box-shadow: none;
  outline: none;
  transition: border-color 0.15s var(--pr-ease), box-shadow 0.15s var(--pr-ease);
}
.pr-tools input[type="search"]:focus { border-color: var(--pr-accent); box-shadow: 0 0 0 3px rgba(69,196,214,0.18); }
.pr-tools input[type="submit"] {
  height: 36px;
  width: auto;
  margin: 0 !important;
  padding: 0 0.9rem;
  border: 1px solid var(--pr-line-2);
  border-radius: 9px;
  background: transparent;
  color: var(--pr-muted);
  font-family: var(--pr-font);
  font-size: 0.78rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s var(--pr-ease), color 0.15s var(--pr-ease);
}
.pr-tools input[type="submit"]:hover { background: rgba(255,255,255,0.05); color: var(--pr-text); }
.pr-per-page {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.66rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--pr-faint);
  white-space: nowrap;
}
.pr-per-page select {
  height: 36px;
  width: auto;
  padding: 0 0.5rem;
  border: 1px solid var(--pr-line-2);
  border-radius: 9px;
  background: var(--pr-surface);
  color: var(--pr-text);
  font-family: var(--pr-font);
  font-size: 0.78rem;
  outline: none;
}
.pr-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  height: 36px;
  padding: 0 1rem;
  border-radius: 9px;
  font-family: var(--pr-font);
  font-size: 0.8rem;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  box-shadow: none;
  transition: filter 0.15s var(--pr-ease), background 0.15s var(--pr-ease),
              color 0.15s var(--pr-ease), border-color 0.15s var(--pr-ease),
              transform 0.12s var(--pr-ease);
}
.pr-btn:active { transform: translateY(1px) scale(0.985); }
.pr-btn--ghost { background: transparent !important; border: 1px solid var(--pr-line-2) !important; color: var(--pr-muted) !important; }
.pr-btn--ghost:hover { background: rgba(255,255,255,0.05) !important; color: var(--pr-text) !important; }
.pr-btn--new {
  border: 1px solid var(--pr-accent) !important;
  background: var(--pr-accent) !important;
  color: #08252a !important;
  font-weight: 700;
}
.pr-btn--new:hover { filter: brightness(1.08); color: #08252a !important; }

/* ── Flashdata + pagination ──────────────────────────────── */
.pr-shell .flashdata {
  display: block;
  margin: 0 0 0.9rem;
  padding: 0.55rem 0.8rem;
  background: rgba(46,194,126,0.12);
  border: 1px solid rgba(46,194,126,0.35);
  border-radius: 8px;
  color: var(--pr-green);
  font-size: 0.78rem;
}
.pr-shell .pagination {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  flex-wrap: wrap;
  margin: 0 0 1rem;
  font-family: var(--pr-mono);
  font-size: 0.74rem;
  color: var(--pr-faint);
}
.pr-shell .pagination a, .pr-shell .pagination span {
  display: inline-flex;
  align-items: center;
  padding: 0.3rem 0.6rem;
  border-radius: 7px;
  color: var(--pr-muted);
  text-decoration: none;
  background: transparent;
  border: 1px solid transparent;
}
.pr-shell .pagination a:hover { color: var(--pr-text); background: rgba(255,255,255,0.05); }
.pr-shell .pagination .active, .pr-shell .pagination a.active {
  background: var(--pr-accent);
  color: #08252a;
  font-weight: 700;
}

/* ── Table ───────────────────────────────────────────────── */
.pr-table-wrap {
  border: 1px solid var(--pr-line-2);
  border-radius: 12px;
  overflow: hidden;
  background: var(--pr-surface);
  animation: pr-fade 0.4s var(--pr-ease) both;
}
table.pr-table {
  width: 100%;
  border-collapse: collapse;
  background: transparent;
  margin: 0;
  font-family: var(--pr-font);
}
table.pr-table thead th {
  background: var(--pr-surface-2);
  color: var(--pr-faint);
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  text-align: left;
  padding: 0.6rem 0.9rem;
  border: none;
  border-bottom: 1px solid var(--pr-line-2);
}
table.pr-table thead th.pr-num, table.pr-table td.pr-num { text-align: right; }
table.pr-table tbody td {
  padding: 0.6rem 0.9rem;
  border: none;
  border-bottom: 1px solid var(--pr-line);
  color: var(--pr-text);
  font-size: 0.82rem;
  vertical-align: middle;
  background: transparent;
}
table.pr-table tbody tr:last-child td { border-bottom: 0; }
table.pr-table tbody tr { transition: background 0.12s var(--pr-ease); }
table.pr-table tbody tr:hover td { background: rgba(255,255,255,0.03); }

.pr-thumb {
  width: 46px;
  height: 46px;
  border-radius: 8px;
  object-fit: cover;
  display: block;
  border: 1px solid var(--pr-line-2);
  background: var(--pr-bg);
}
.pr-thumb--empty {
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--pr-faint);
}
.pr-name { font-weight: 600; letter-spacing: -0.01em; color: var(--pr-text); }
.pr-sub {
  display: block;
  margin-top: 0.15rem;
  font-size: 0.7rem;
  color: var(--pr-faint);
  max-width: 38ch;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.pr-price { font-family: var(--pr-mono); font-feature-settings: 'tnum' 1; font-weight: 600; }
.pr-price del { color: var(--pr-faint); font-weight: 500; margin-right: 0.4rem; }
.pr-price ins { color: var(--pr-accent); text-decoration: none; }

.pr-stock { font-family: var(--pr-mono); font-feature-settings: 'tnum' 1; font-weight: 600; }
.pr-stock--in { color: var(--pr-green); }
.pr-stock--out { color: var(--pr-red); }

.pr-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.2rem 0.55rem;
  border-radius: 99px;
  font-size: 0.66rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  text-transform: capitalize;
}
.pr-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
.pr-badge--active   { color: var(--pr-green); background: rgba(46,194,126,0.12); }
.pr-badge--inactive { color: var(--pr-faint); background: rgba(255,255,255,0.06); }
.pr-badge--archived { color: var(--pr-amber); background: rgba(224,166,75,0.12); }

.pr-cats { display: flex; flex-wrap: wrap; gap: 0.3rem; }
.pr-cat {
  display: inline-flex;
  padding: 0.12rem 0.5rem;
  border-radius: 99px;
  font-size: 0.66rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  text-transform: capitalize;
  color: var(--pr-accent);
  background: rgba(69,196,214,0.1);
  border: 1px solid rgba(69,196,214,0.25);
}
.pr-cat--none { color: var(--pr-faint); background: transparent; border-color: var(--pr-line-2); text-transform: none; }

.pr-row-action {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.3rem 0.7rem;
  border: 1px solid var(--pr-line-2) !important;
  border-radius: 8px;
  background: transparent !important;
  color: var(--pr-muted) !important;
  font-size: 0.72rem;
  font-weight: 600;
  text-decoration: none;
  transition: background 0.12s var(--pr-ease), color 0.12s var(--pr-ease), border-color 0.12s var(--pr-ease);
}
.pr-row-action:hover { background: rgba(69,196,214,0.12) !important; color: var(--pr-accent) !important; border-color: rgba(69,196,214,0.4) !important; }

/* ── Empty state ─────────────────────────────────────────── */
.pr-empty {
  padding: 3rem 1.5rem;
  text-align: center;
  border: 1px dashed var(--pr-line-2);
  border-radius: 12px;
  color: var(--pr-muted);
}
.pr-empty__title { margin: 0 0 0.4rem; font-size: 0.95rem; font-weight: 600; color: var(--pr-text); }
.pr-empty p { margin: 0; font-size: 0.8rem; }

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 48rem) {
  .pr-shell { padding: 1rem; border-radius: 12px; }
  .pr-tools { width: 100%; }
  .pr-tools form { flex: 1; }
  .pr-tools input[type="search"] { flex: 1; width: auto; }
  .pr-hide-sm { display: none; }
  .pr-sub { max-width: 22ch; }
}
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
}
</style>

<div class="pr-shell">

    <div class="pr-head">
        <div>
            <p class="pr-eyebrow">Molas Surf Club &middot; Shop</p>
            <h2 class="pr-title">Products</h2>
        </div>
        <div class="pr-tools" id="results-tbl">
            <?php
            echo form_open('products/manage/1/', array("method" => "get"));
            echo form_search('searchphrase', '', array("placeholder" => "Search products..."));
            echo form_submit('submit', 'Search');
            echo form_close();
            ?>
            <label class="pr-per-page">
                Per page
                <?php
                $dropdown_attr['onchange'] = 'setPerPage()';
                echo form_dropdown('per_page', $per_page_options, $selected_per_page, $dropdown_attr);
                ?>
            </label>
            <?= anchor('products/orders', 'Orders', array("class" => "pr-btn pr-btn--ghost")) ?>
            <?php if (strtolower(ENV) === 'dev'): ?>
            <?= anchor('api/explorer/products', 'API', array("class" => "pr-btn pr-btn--ghost")) ?>
            <?php endif; ?>
            <?= anchor('products/create', '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg> New product', array("class" => "pr-btn pr-btn--new")) ?>
        </div>
    </div>

    <?php flashdata(); ?>
    <?= Pagination::display($pagination_data) ?>

    <?php if (count($rows) > 0): ?>
        <div class="pr-table-wrap">
            <table class="pr-table">
                <thead>
                    <tr>
                        <th style="width:46px;"></th>
                        <th>Product</th>
                        <th class="pr-num">Price</th>
                        <th class="pr-num pr-hide-sm">Stock</th>
                        <th class="pr-hide-sm">Status</th>
                        <th class="pr-hide-sm">Categories</th>
                        <th style="width:1%;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row):
                        $cover = (!empty($row->image))
                            ? BASE_URL . 'products_module/images/products_pics/' . $row->id . '/' . rawurlencode($row->image)
                            : '';
                        $has_discount = isset($row->discount_price) && (float) $row->discount_price > 0;
                        $stock = (int) $row->in_stock;
                        $status = strtolower((string) $row->status);
                        $status_class = in_array($status, ['active', 'inactive', 'archived'], true) ? $status : 'inactive';
                    ?>
                    <tr>
                        <td>
                            <?php if ($cover !== ''): ?>
                                <img class="pr-thumb" src="<?= $cover ?>" alt="" loading="lazy">
                            <?php else: ?>
                                <span class="pr-thumb pr-thumb--empty" title="No image">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.1-3.1a2 2 0 0 0-2.8 0L6 21"/></svg>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="pr-name"><?= out($row->name) ?></span>
                            <?php if (!empty($row->short_desc)): ?>
                                <span class="pr-sub"><?= out($row->short_desc) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="pr-num pr-price">
                            <?php if ($has_discount): ?>
                                <del>&euro;<?= number_format((float) $row->price, 2) ?></del><ins>&euro;<?= number_format((float) $row->discount_price, 2) ?></ins>
                            <?php else: ?>
                                &euro;<?= number_format((float) $row->price, 2) ?>
                            <?php endif; ?>
                        </td>
                        <td class="pr-num pr-hide-sm">
                            <span class="pr-stock <?= $stock > 0 ? 'pr-stock--in' : 'pr-stock--out' ?>"><?= $stock ?></span>
                        </td>
                        <td class="pr-hide-sm">
                            <span class="pr-badge pr-badge--<?= $status_class ?>"><?= out($row->status) ?></span>
                        </td>
                        <td class="pr-hide-sm">
                            <?php if (!empty($row->categories)): ?>
                                <div class="pr-cats">
                                    <?php foreach ($row->categories as $cat): ?>
                                        <span class="pr-cat"><?= out($cat) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span class="pr-cat pr-cat--none">none</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= anchor('products/show/' . $row->id, 'View', array("class" => "pr-row-action")) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (count($rows) > 9):
            unset($pagination_data['include_showing_statement']);
            echo Pagination::display($pagination_data);
        endif; ?>

    <?php else: ?>
        <div class="pr-empty">
            <p class="pr-empty__title">No products found</p>
            <p>Create one with the &ldquo;New product&rdquo; button, or adjust the search.</p>
        </div>
    <?php endif; ?>

</div><!-- /.pr-shell -->
</div><!-- /#products-container -->
