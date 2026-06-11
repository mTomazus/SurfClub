<?php
/**
 * MX fragment for #events-container (mx-select="#ev-payload").
 * Also carries a fresh #stat-panel which the container OOB-swaps in,
 * so the stats stay live after every add/delete.
 */
require_once APPPATH . 'modules/events/views/_ev_helpers.php';

echo ev_render_stat_panel($rows);
?>
<div id="ev-payload">
    <?= ev_render_board($rows) ?>
</div>
