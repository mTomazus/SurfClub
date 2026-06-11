<?php
/**
 * Shared render helpers for the Events admin panel.
 *
 * Plain guarded functions (same pattern as the other admin modules) so the
 * stat panel and the table render identically from index.php and from the
 * fetch_table MX endpoint — which lets the table fetch OOB-swap fresh stats
 * into #stat-panel after every add/delete.
 *
 * $rows: id, title, description, start_time, phone, email
 */

if (!function_exists('ev_render_stat_panel')) {
    function ev_render_stat_panel(array $rows): string {
        $now      = time();
        $total    = count($rows);
        $upcoming = 0;
        $next     = null;
        foreach ($rows as $row) {
            $ts = strtotime($row->start_time);
            if ($ts !== false && $ts >= $now) {
                $upcoming++;
                if ($next === null || $ts < strtotime($next->start_time)) {
                    $next = $row;
                }
            }
        }
        $past = $total - $upcoming;

        ob_start();
        ?>
<div id="stat-panel">
    <div class="ev-stats">
        <div class="ev-stat ev-stat--lead">
            <span class="ev-stat__label">Next event</span>
            <?php if ($next): ?>
                <span class="ev-stat__next"><?= out($next->title) ?></span>
                <span class="ev-stat__when"><?= date('D, M j · H:i', strtotime($next->start_time)) ?></span>
            <?php else: ?>
                <span class="ev-stat__next ev-stat__next--none">Nothing scheduled</span>
                <span class="ev-stat__when">&nbsp;</span>
            <?php endif; ?>
        </div>
        <div class="ev-stat"><span class="ev-stat__num"><?= $total ?></span><span class="ev-stat__label">Events</span></div>
        <div class="ev-stat ev-stat--up"><span class="ev-stat__num"><?= $upcoming ?></span><span class="ev-stat__label">Upcoming</span></div>
        <div class="ev-stat ev-stat--past"><span class="ev-stat__num"><?= $past ?></span><span class="ev-stat__label">Past</span></div>
    </div>
</div>
        <?php
        return ob_get_clean();
    }
}

if (!function_exists('ev_render_board')) {
    function ev_render_board(array $rows): string {
        ob_start();
        if (empty($rows)) {
            ?>
<div class="ev-empty">
    <p class="ev-empty__title">No events scheduled</p>
    <p>Create the first event with the &ldquo;New event&rdquo; button above.</p>
</div>
            <?php
            return ob_get_clean();
        }

        $now = time();
        ?>
<div class="ev-board">
    <table class="ev-table">
        <thead>
            <tr>
                <th>When</th>
                <th>Event</th>
                <th>Contact</th>
                <th class="ev-th-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0; foreach ($rows as $row):
                $ts      = strtotime($row->start_time);
                $is_past = ($ts !== false && $ts < $now);
            ?>
            <tr style="--i:<?= $i++ ?>" class="<?= $is_past ? 'ev-row--past' : '' ?>">
                <td>
                    <span class="ev-when">
                        <b><?= $ts !== false ? date('M j', $ts) : '?' ?></b>
                        <span><?= $ts !== false ? date('H:i', $ts) : '' ?></span>
                    </span>
                    <?php if ($is_past): ?><span class="ev-past-badge">Past</span><?php endif; ?>
                </td>
                <td>
                    <span class="ev-title"><?= out($row->title) ?></span>
                    <span class="ev-sub"><?= out($row->description) ?></span>
                </td>
                <td>
                    <a class="ev-tel" href="tel:<?= out($row->phone) ?>"><?= out($row->phone) ?></a>
                    <span class="ev-sub"><?= out($row->email) ?></span>
                </td>
                <td>
                    <div class="ev-actions">
                        <button type="button" class="ev-iconbtn" title="Edit event" aria-label="Edit event <?= (int) $row->id ?>"
                                mx-get="events/event_form/<?= $row->id ?>"
                                mx-build-modal='{"id": "event-modal","modalHeading": "Edit Event Schedule"}'>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                        </button>
                        <button type="button" class="ev-iconbtn ev-iconbtn--danger" title="Delete event" aria-label="Delete event <?= (int) $row->id ?>"
                                mx-post="events/delete_modal/<?= $row->id ?>"
                                mx-build-modal='{"id": "event-delete-modal","modalHeading": "Delete Event Schedule"}'
                                mx-on-success="#events-container"
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
