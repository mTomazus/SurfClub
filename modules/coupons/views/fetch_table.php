<?php
/**
 * MX fragment for #coupons-container (mx-select="#cu-payload").
 * Also carries a fresh #stat-panel which the container OOB-swaps in,
 * so the stats stay live after every add/delete.
 */
require_once APPPATH . 'modules/coupons/views/_cu_helpers.php';

echo cu_render_stat_panel($rows);
?>
<div id="cu-payload">
    <?= cu_render_board($rows) ?>
</div>
