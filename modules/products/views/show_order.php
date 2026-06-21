<?php
/**
 * Products admin — single order (admin_area template).
 * Charcoal+cyan styling; right rail carries the fulfillment workflow buttons
 * (post to products/update_order_status/{id}; transitions guarded server-side).
 */
$badge = static function (string $status): string {
    $known = ['pending', 'paid', 'shipped', 'completed', 'failed', 'cancelled'];
    $cls = in_array(strtolower($status), $known, true) ? strtolower($status) : 'pending';
    return '<span class="po-badge po-badge--' . $cls . '">' . htmlspecialchars($status) . '</span>';
};
$total = 0;
foreach ($items as $it) { $total += (float) $it->price * (int) $it->quantity; }
$delivery_label = ($order->delivery === 'omniva')
    ? 'Omniva paštomatas' . (!empty($order->address) ? ' · ' . $order->address : '')
    : 'Atsiėmimas (Vėtros g. 8)';
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

.po { margin:0.9rem; padding:clamp(1.25rem,2vw,1.85rem); background:var(--po-bg); border:1px solid var(--po-line); border-radius:16px; color:var(--po-text); font-family:var(--po-font); animation:po-fade 0.4s var(--po-ease) both; }
.po .flashdata { display:block; margin:0 0 0.9rem; padding:0.55rem 0.8rem; background:rgba(46,194,126,0.12); border:1px solid rgba(46,194,126,0.35); border-radius:8px; color:var(--po-green); font-size:0.78rem; }

.po-head { display:flex; align-items:flex-end; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-bottom:1.1rem; }
.po-eyebrow { margin:0 0 0.45rem; font-family:var(--po-mono); font-size:0.62rem; font-weight:600; text-transform:uppercase; letter-spacing:0.16em; color:var(--po-accent); }
.po-title { margin:0; display:flex; align-items:baseline; gap:0.6rem; flex-wrap:wrap; font-size:clamp(1.3rem,2.4vw,1.7rem); font-weight:700; letter-spacing:-0.03em; }
.po-btn { display:inline-flex; align-items:center; gap:0.4rem; height:36px; padding:0 0.95rem !important; margin:0 !important; border-radius:9px; font-family:var(--po-font) !important; font-size:0.78rem; font-weight:600; text-decoration:none; cursor:pointer; box-shadow:none; }
.po-btn--ghost { background:transparent !important; border:1px solid var(--po-line-2) !important; color:var(--po-muted) !important; }
.po-btn--ghost:hover { background:rgba(255,255,255,0.05) !important; color:var(--po-text) !important; }

.po-grid { display:grid; grid-template-columns:1fr 300px; gap:1rem; align-items:start; }
.po-panel { border:1px solid var(--po-line-2); border-radius:12px; background:var(--po-surface); overflow:hidden; margin-bottom:1rem; }
.po-panel__head { font-size:0.62rem; font-weight:600; text-transform:uppercase; letter-spacing:0.12em; color:var(--po-faint); background:var(--po-surface-2); padding:0.6rem 0.9rem; }
.po-panel__body { padding:0.9rem; }

.po-kv { display:flex; justify-content:space-between; gap:1rem; padding:0.5rem 0; border-bottom:1px solid var(--po-line); font-size:0.82rem; }
.po-kv:last-child { border-bottom:0; }
.po-kv > span:first-child { color:var(--po-muted); }
.po-kv > span:last-child { text-align:right; word-break:break-word; }

/* Items table */
table.po-items { width:100%; border-collapse:collapse; background:transparent; margin:0; }
table.po-items thead th { background:var(--po-surface-2); color:var(--po-faint); font-size:0.6rem; font-weight:600; text-transform:uppercase; letter-spacing:0.1em; text-align:left; padding:0.6rem 0.9rem; border:none; border-bottom:1px solid var(--po-line-2); }
table.po-items th.po-num, table.po-items td.po-num { text-align:right; }
table.po-items tbody td { padding:0.6rem 0.9rem; border:none; border-bottom:1px solid var(--po-line); color:var(--po-text); font-size:0.82rem; background:transparent; vertical-align:middle; }
table.po-items tfoot td { padding:0.7rem 0.9rem; font-weight:700; border-top:1px solid var(--po-line-2); }
.po-item-name { font-weight:600; }
.po-item-variant { display:inline-block; margin-top:0.15rem; font-size:0.68rem; color:var(--po-accent); background:rgba(69,196,214,0.1); border:1px solid rgba(69,196,214,0.25); border-radius:99px; padding:0.05rem 0.45rem; text-transform:capitalize; }
.po-money { font-family:var(--po-mono); font-feature-settings:'tnum' 1; }

/* Status badges */
.po-badge { display:inline-flex; align-items:center; gap:0.35rem; padding:0.2rem 0.6rem; border-radius:99px; font-size:0.68rem; font-weight:600; text-transform:capitalize; }
.po-badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
.po-badge--pending{color:var(--po-amber);background:rgba(224,166,75,0.12);} .po-badge--paid{color:var(--po-accent);background:rgba(69,196,214,0.12);}
.po-badge--shipped{color:var(--po-blue);background:rgba(91,141,239,0.14);} .po-badge--completed{color:var(--po-green);background:rgba(46,194,126,0.12);}
.po-badge--failed{color:var(--po-red);background:rgba(229,72,77,0.12);} .po-badge--cancelled{color:var(--po-faint);background:rgba(255,255,255,0.06);}

/* Fulfillment actions */
.po-status-now { display:flex; align-items:center; gap:0.5rem; margin-bottom:0.9rem; font-size:0.82rem; color:var(--po-muted); }
.po-actions form { display:block; margin:0 0 0.5rem; width:auto; grid-template-columns:none; }
.po-actions form:last-child { margin-bottom:0; }
.po-act { width:100%; height:38px; margin:0 !important; border-radius:9px; border:1px solid transparent; font-family:var(--po-font); font-size:0.8rem; font-weight:600; cursor:pointer; transition:filter .15s var(--po-ease), background .15s var(--po-ease); }
.po-act--accent { background:var(--po-accent) !important; color:#08252a !important; }
.po-act--green  { background:var(--po-green) !important; color:#04261a !important; }
.po-act--danger { background:rgba(229,72,77,0.12) !important; border-color:rgba(229,72,77,0.4) !important; color:var(--po-red) !important; }
.po-act--accent:hover, .po-act--green:hover { filter:brightness(1.08); }
.po-act--danger:hover { background:rgba(229,72,77,0.22) !important; color:#fff !important; }
.po-terminal { font-size:0.78rem; color:var(--po-faint); }

@media (max-width:56rem) { .po-grid { grid-template-columns:1fr; } }
@media (max-width:40rem) { .po { padding:1rem; } }
@media (prefers-reduced-motion:reduce) { *,*::before,*::after { animation-duration:.01ms !important; transition-duration:.01ms !important; } }
</style>

<div class="po-head">
    <div>
        <p class="po-eyebrow">Molas Surf Club &middot; Order #<?= (int) $order->id ?></p>
        <h2 class="po-title"><?= out($order->customer_name) ?> <?= $badge($order->status) ?></h2>
    </div>
    <?= anchor('products/orders', '&larr; All orders', ['class' => 'po-btn po-btn--ghost']) ?>
</div>

<?php flashdata(); ?>

<div class="po-grid">

    <!-- Items -->
    <div class="po-panel">
        <div class="po-panel__head">Order items</div>
        <?php if (!empty($items)): ?>
        <table class="po-items">
            <thead>
                <tr><th>Product</th><th class="po-num">Price</th><th class="po-num">Qty</th><th class="po-num">Subtotal</th></tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): $sub = (float) $item->price * (int) $item->quantity; ?>
                <tr>
                    <td>
                        <span class="po-item-name"><?= out($item->name) ?></span>
                        <?php if (!empty($item->option_value)): ?>
                            <br><span class="po-item-variant"><?= out(ucfirst($item->option_name)) ?>: <?= out($item->option_value) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="po-num po-money">&euro;<?= number_format((float) $item->price, 2) ?></td>
                    <td class="po-num po-money"><?= (int) $item->quantity ?></td>
                    <td class="po-num po-money">&euro;<?= number_format($sub, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><td colspan="3" class="po-num">Total</td><td class="po-num po-money">&euro;<?= number_format($total, 2) ?></td></tr>
            </tfoot>
        </table>
        <?php else: ?>
        <div class="po-panel__body"><p style="margin:0;color:var(--po-muted);font-size:0.82rem;">No items recorded for this order.</p></div>
        <?php endif; ?>
    </div>

    <!-- Right rail -->
    <div>
        <div class="po-panel">
            <div class="po-panel__head">Fulfillment</div>
            <div class="po-panel__body">
                <div class="po-status-now">Current: <?= $badge($order->status) ?></div>
                <div class="po-actions">
                    <?php if (!empty($allowed_transitions)): ?>
                        <?php foreach ($allowed_transitions as $st):
                            $label = $status_actions[$st] ?? ('Mark ' . $st);
                            $cls = $st === 'cancelled' ? 'po-act po-act--danger' : ($st === 'completed' ? 'po-act po-act--green' : 'po-act po-act--accent');
                            echo form_open('products/update_order_status/' . $order->id);
                            echo '<input type="hidden" name="status" value="' . out($st) . '">';
                            echo '<button type="submit" class="' . $cls . '">' . out($label) . '</button>';
                            echo form_close();
                        endforeach; ?>
                    <?php else: ?>
                        <p class="po-terminal">No further actions — this order is <?= out($order->status) ?>.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="po-panel">
            <div class="po-panel__head">Customer</div>
            <div class="po-panel__body">
                <div class="po-kv"><span>Name</span><span><?= out($order->customer_name) ?></span></div>
                <div class="po-kv"><span>Email</span><span><?= out($order->email) ?></span></div>
                <div class="po-kv"><span>Phone</span><span><?= out($order->phone) ?></span></div>
            </div>
        </div>

        <div class="po-panel">
            <div class="po-panel__head">Shipping &amp; payment</div>
            <div class="po-panel__body">
                <div class="po-kv"><span>Delivery</span><span><?= out($delivery_label) ?></span></div>
                <?php if (!empty($order->payment_reference)): ?>
                <div class="po-kv"><span>Payment ref</span><span><?= out($order->payment_reference) ?></span></div>
                <?php endif; ?>
                <?php if (!empty($order->created_at)): ?>
                <div class="po-kv"><span>Placed</span><span><?= date('Y-m-d H:i', strtotime($order->created_at)) ?></span></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

</div><!-- /.po -->
