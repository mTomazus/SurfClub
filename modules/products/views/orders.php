<?php
/**
 * Products admin — orders list (admin_area template).
 * Self-contained charcoal+cyan styling matching the products manage/show panels.
 * Rows carry ->total and ->item_count (computed in the controller).
 */
$badge = static function (string $status): string {
    $s = strtolower($status);
    $known = ['pending', 'paid', 'shipped', 'completed', 'failed', 'cancelled'];
    $cls = in_array($s, $known, true) ? $s : 'pending';
    return '<span class="po-badge po-badge--' . $cls . '">' . htmlspecialchars($status) . '</span>';
};
$status_labels = ['pending' => 'Pending', 'paid' => 'Paid', 'shipped' => 'Shipped', 'completed' => 'Completed', 'failed' => 'Failed', 'cancelled' => 'Cancelled'];
$revenue = 0;
foreach ($rows as $r) { if (in_array($r->status, ['paid', 'shipped', 'completed'], true)) { $revenue += (float) $r->total; } }
?>
<div class="po">

<style>
@import url('https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600;700&display=swap');

:root {
  --po-bg:#15141a; --po-surface:#1c1b23; --po-surface-2:#211f2a;
  --po-line:rgba(255,255,255,0.06); --po-line-2:rgba(255,255,255,0.11);
  --po-text:#f4f3f8; --po-muted:#a09db1; --po-faint:#6c6979;
  --po-accent:#45c4d6; --po-green:#2ec27e; --po-amber:#e0a64b;
  --po-blue:#5b8def; --po-red:#e5484d;
  --po-font:'Geist','Segoe UI',system-ui,-apple-system,sans-serif;
  --po-mono:'Geist Mono','SF Mono',ui-monospace,Menlo,monospace;
  --po-ease:cubic-bezier(0.16,1,0.3,1);
}
@keyframes po-fade { from { opacity:0; } }

.po {
  margin:0.9rem; padding:clamp(1.25rem,2vw,1.85rem);
  background:var(--po-bg); border:1px solid var(--po-line); border-radius:16px;
  color:var(--po-text); font-family:var(--po-font); animation:po-fade 0.4s var(--po-ease) both;
}
.po .flashdata {
  display:block; margin:0 0 0.9rem; padding:0.55rem 0.8rem;
  background:rgba(46,194,126,0.12); border:1px solid rgba(46,194,126,0.35);
  border-radius:8px; color:var(--po-green); font-size:0.78rem;
}

/* Header */
.po-head { display:flex; align-items:flex-end; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-bottom:1.1rem; }
.po-eyebrow { margin:0 0 0.45rem; font-family:var(--po-mono); font-size:0.62rem; font-weight:600; text-transform:uppercase; letter-spacing:0.16em; color:var(--po-accent); }
.po-title { margin:0; font-size:clamp(1.4rem,2.5vw,1.8rem); font-weight:700; line-height:1; letter-spacing:-0.03em; }
.po-stats { display:flex; gap:1.5rem; align-items:baseline; }
.po-stat__num { font-family:var(--po-mono); font-feature-settings:'tnum' 1; font-size:1.4rem; font-weight:700; color:var(--po-accent); }
.po-stat__num--rev { color:var(--po-green); }
.po-stat__label { display:block; font-size:0.58rem; font-weight:600; text-transform:uppercase; letter-spacing:0.1em; color:var(--po-muted); }

/* Buttons */
.po-btn { display:inline-flex; align-items:center; gap:0.4rem; height:34px; padding:0 0.9rem !important; margin:0 !important; border-radius:9px; font-family:var(--po-font) !important; font-size:0.78rem; font-weight:600; text-decoration:none; cursor:pointer; box-shadow:none; transition:background .15s var(--po-ease),color .15s var(--po-ease),border-color .15s var(--po-ease); }
.po-btn--ghost { background:transparent !important; border:1px solid var(--po-line-2) !important; color:var(--po-muted) !important; }
.po-btn--ghost:hover { background:rgba(255,255,255,0.05) !important; color:var(--po-text) !important; }

/* Filter chips */
.po-filters { display:flex; flex-wrap:wrap; gap:0.4rem; margin:0 0 1rem; }
.po-chip {
  display:inline-flex; align-items:center; gap:0.4rem; height:30px; padding:0 0.7rem;
  border:1px solid var(--po-line-2); border-radius:99px; background:var(--po-surface);
  color:var(--po-muted); font-size:0.74rem; font-weight:600; text-decoration:none;
  transition:border-color .15s var(--po-ease), color .15s var(--po-ease), background .15s var(--po-ease);
}
.po-chip:hover { border-color:rgba(69,196,214,0.5); color:var(--po-text); }
.po-chip.is-active { background:var(--po-accent); border-color:var(--po-accent); color:#08252a; }
.po-chip__n { font-family:var(--po-mono); font-size:0.68rem; opacity:0.8; }

/* Pagination */
.po .pagination { display:flex; align-items:center; gap:0.25rem; flex-wrap:wrap; margin:0 0 1rem; font-family:var(--po-mono); font-size:0.74rem; color:var(--po-faint); }
.po .pagination a, .po .pagination span { display:inline-flex; align-items:center; padding:0.3rem 0.6rem; border-radius:7px; color:var(--po-muted); text-decoration:none; border:1px solid transparent; }
.po .pagination a:hover { color:var(--po-text); background:rgba(255,255,255,0.05); }
.po .pagination .active { background:var(--po-accent); color:#08252a; font-weight:700; }

/* Table */
.po-table-wrap { border:1px solid var(--po-line-2); border-radius:12px; overflow:hidden; background:var(--po-surface); animation:po-fade 0.4s var(--po-ease) both; }
table.po-table { width:100%; border-collapse:collapse; background:transparent; margin:0; font-family:var(--po-font); }
table.po-table thead th { background:var(--po-surface-2); color:var(--po-faint); font-size:0.6rem; font-weight:600; text-transform:uppercase; letter-spacing:0.1em; text-align:left; padding:0.6rem 0.9rem; border:none; border-bottom:1px solid var(--po-line-2); }
table.po-table th.po-num, table.po-table td.po-num { text-align:right; }
table.po-table tbody td { padding:0.6rem 0.9rem; border:none; border-bottom:1px solid var(--po-line); color:var(--po-text); font-size:0.82rem; vertical-align:middle; background:transparent; }
table.po-table tbody tr:last-child td { border-bottom:0; }
table.po-table tbody tr { transition:background .12s var(--po-ease); }
table.po-table tbody tr:hover td { background:rgba(255,255,255,0.03); }
.po-id { font-family:var(--po-mono); color:var(--po-faint); }
.po-cust { font-weight:600; }
.po-sub { display:block; font-size:0.7rem; color:var(--po-faint); }
.po-money { font-family:var(--po-mono); font-feature-settings:'tnum' 1; font-weight:600; }
.po-date { font-family:var(--po-mono); font-size:0.74rem; color:var(--po-faint); }

/* Status badges */
.po-badge { display:inline-flex; align-items:center; gap:0.35rem; padding:0.18rem 0.55rem; border-radius:99px; font-size:0.66rem; font-weight:600; text-transform:capitalize; }
.po-badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
.po-badge--pending   { color:var(--po-amber); background:rgba(224,166,75,0.12); }
.po-badge--paid      { color:var(--po-accent); background:rgba(69,196,214,0.12); }
.po-badge--shipped   { color:var(--po-blue); background:rgba(91,141,239,0.14); }
.po-badge--completed { color:var(--po-green); background:rgba(46,194,126,0.12); }
.po-badge--failed    { color:var(--po-red); background:rgba(229,72,77,0.12); }
.po-badge--cancelled { color:var(--po-faint); background:rgba(255,255,255,0.06); }

.po-row-action { display:inline-flex; align-items:center; padding:0.3rem 0.7rem; border:1px solid var(--po-line-2) !important; border-radius:8px; background:transparent !important; color:var(--po-muted) !important; font-size:0.72rem; font-weight:600; text-decoration:none; transition:background .12s,color .12s,border-color .12s; }
.po-row-action:hover { background:rgba(69,196,214,0.12) !important; color:var(--po-accent) !important; border-color:rgba(69,196,214,0.4) !important; }

.po-empty { padding:3rem 1.5rem; text-align:center; border:1px dashed var(--po-line-2); border-radius:12px; color:var(--po-muted); }

@media (max-width:48rem) { .po { padding:1rem; } .po-hide-sm { display:none; } }
@media (prefers-reduced-motion:reduce) { *,*::before,*::after { animation-duration:.01ms !important; transition-duration:.01ms !important; } }
</style>

<div class="po-head">
    <div>
        <p class="po-eyebrow">Molas Surf Club &middot; Shop orders</p>
        <h2 class="po-title">Orders</h2>
    </div>
    <div class="po-stats">
        <div><span class="po-stat__num"><?= array_sum($status_counts) ?></span><span class="po-stat__label">Total orders</span></div>
        <div><span class="po-stat__num po-stat__num--rev">&euro;<?= number_format($revenue, 2) ?></span><span class="po-stat__label">Revenue (this page)</span></div>
        <?= anchor('products/manage', '&larr; Products', ['class' => 'po-btn po-btn--ghost']) ?>
    </div>
</div>

<?php flashdata(); ?>

<div class="po-filters">
    <?php
    $all_cls = $status_filter === '' ? 'po-chip is-active' : 'po-chip';
    echo anchor('products/orders', 'All <span class="po-chip__n">' . array_sum($status_counts) . '</span>', ['class' => $all_cls]);
    foreach ($status_labels as $key => $label) {
        $n = $status_counts[$key] ?? 0;
        if ($n === 0 && $status_filter !== $key) { continue; }
        $cls = $status_filter === $key ? 'po-chip is-active' : 'po-chip';
        echo anchor('products/orders?status=' . $key, $label . ' <span class="po-chip__n">' . $n . '</span>', ['class' => $cls]);
    }
    ?>
</div>

<?= Pagination::display($pagination_data) ?>

<?php if (count($rows) > 0): ?>
<div class="po-table-wrap">
    <table class="po-table">
        <thead>
            <tr>
                <th style="width:1%;">#</th>
                <th>Customer</th>
                <th class="po-num po-hide-sm">Items</th>
                <th class="po-num">Total</th>
                <th class="po-hide-sm">Delivery</th>
                <th>Status</th>
                <th class="po-hide-sm">Date</th>
                <th style="width:1%;"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
            <tr>
                <td class="po-id"><?= (int) $row->id ?></td>
                <td>
                    <span class="po-cust"><?= out($row->customer_name) ?></span>
                    <span class="po-sub"><?= out($row->email) ?></span>
                </td>
                <td class="po-num po-hide-sm"><?= (int) $row->item_count ?></td>
                <td class="po-num po-money">&euro;<?= number_format((float) $row->total, 2) ?></td>
                <td class="po-hide-sm"><?= out($row->delivery) ?></td>
                <td><?= $badge($row->status) ?></td>
                <td class="po-hide-sm po-date"><?= !empty($row->created_at) ? date('Y-m-d H:i', strtotime($row->created_at)) : '—' ?></td>
                <td><?= anchor('products/show_order/' . $row->id, 'View', ['class' => 'po-row-action']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
if (count($rows) > 9) {
    unset($pagination_data['include_showing_statement']);
    echo Pagination::display($pagination_data);
}
?>
<?php else: ?>
<div class="po-empty">No orders found<?= $status_filter ? ' with status “' . out($status_filter) . '”' : '' ?>.</div>
<?php endif; ?>

</div><!-- /.po -->
