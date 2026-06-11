<?php
/**
 * Shared render helpers for the Coupons admin panel.
 *
 * Plain guarded functions (same pattern as staff_calendar/_sc_helpers.php)
 * so the stat panel and the table render identically from index_coupon.php
 * and from the fetch_table MX endpoint — which lets the table fetch OOB-swap
 * fresh stats into #stat-panel after every add/delete.
 */

if (!function_exists('cu_render_stat_panel')) {
    function cu_render_stat_panel(array $rows): string {
        $total  = count($rows);
        $active = 0;
        $value  = 0.0;
        foreach ($rows as $row) {
            if (strtolower((string) $row->status) === 'active') $active++;
            $value += (float) $row->price;
        }
        $other = $total - $active;

        ob_start();
        ?>
<div id="stat-panel">
    <div class="cu-stats">
        <div class="cu-stat cu-stat--lead">
            <span class="cu-stat__label">Total value</span>
            <span class="cu-stat__num"><?= number_format($value, 0) ?><small>&euro;</small></span>
        </div>
        <div class="cu-stat"><span class="cu-stat__num"><?= $total ?></span><span class="cu-stat__label">Coupons</span></div>
        <div class="cu-stat cu-stat--active"><span class="cu-stat__num"><?= $active ?></span><span class="cu-stat__label">Active</span></div>
        <div class="cu-stat cu-stat--other"><span class="cu-stat__num"><?= $other ?></span><span class="cu-stat__label">Inactive</span></div>
    </div>
</div>
        <?php
        return ob_get_clean();
    }
}

if (!function_exists('cu_render_board')) {
    function cu_render_board(array $rows): string {
        ob_start();
        if (empty($rows)) {
            ?>
<div class="cu-empty">
    <p class="cu-empty__title">No coupons yet</p>
    <p>Create the first gift coupon with the &ldquo;New coupon&rdquo; button above.</p>
</div>
            <?php
            return ob_get_clean();
        }
        ?>
<div class="cu-board">
    <table class="cu-table" id="all-coupons">
        <thead>
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Name</th>
                <th>Price</th>
                <th>Status</th>
                <th class="cu-th-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0; foreach ($rows as $row):
                $st = strtolower((string) $row->status);
                $badge_class = ($st === 'active') ? 'cu-badge--active' : 'cu-badge--other';
                $date_ts  = !empty($row->date_formed) ? strtotime($row->date_formed) : false;
                $date_sub = ($date_ts !== false && $date_ts > 0) ? date('Y-m-d', $date_ts) : '';
            ?>
            <tr style="--i:<?= $i++ ?>">
                <td>
                    <span class="cu-code"><?= date('Y') . '-' . (int) $row->id ?></span>
                    <?php if ($date_sub): ?><span class="cu-sub"><?= $date_sub ?></span><?php endif; ?>
                </td>
                <td><?= out($row->coupon_type) ?></td>
                <td>
                    <span class="cu-name"><?= out($row->name) ?></span>
                    <span class="cu-sub"><?= out($row->email) ?></span>
                </td>
                <td><span class="cu-price"><?= out($row->price) ?> &euro;</span></td>
                <td><span class="cu-badge <?= $badge_class ?>"><i></i><?= out(ucfirst((string) $row->status)) ?></span></td>
                <td>
                    <div class="cu-actions">
                        <button type="button" class="cu-iconbtn" title="Edit coupon" aria-label="Edit coupon <?= (int) $row->id ?>"
                                mx-get="coupons/coupon_form/<?= $row->id ?>"
                                mx-build-modal='{"id": "event-modal","modalHeading": "Edit Coupon"}'>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                        </button>
                        <button type="button" class="cu-iconbtn cu-iconbtn--danger" title="Delete coupon" aria-label="Delete coupon <?= (int) $row->id ?>"
                                mx-post="coupons/delete_modal/<?= $row->id ?>"
                                mx-build-modal='{"id": "delete-modal","modalHeading": "Delete Coupon"}'
                                mx-on-success="#coupons-container"
                                mx-target="#information">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
        <?php
        return ob_get_clean();
    }
}
