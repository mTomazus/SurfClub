<?php
/**
 * Shared render helpers for the Lessons admin panel.
 *
 * Plain guarded functions (same pattern as staff_calendar/_sc_helpers.php)
 * so the stat panel and the schedule table render identically from
 * lessons_index.php and from the fetch_lessons MX endpoint — which lets the
 * table fetch OOB-swap fresh stats into #stat-panel after every add/delete.
 *
 * $rows: id, name, date, start_time, available_places, reserved_places
 * (upcoming schedules only, ordered by date).
 */

if (!function_exists('ls_render_stat_panel')) {
    function ls_render_stat_panel(array $rows): string {
        $today_str = date('Y-m-d');
        $sessions  = count($rows);
        $today     = 0;
        $capacity  = 0;
        $reserved  = 0;
        foreach ($rows as $row) {
            if ($row->date === $today_str) $today++;
            $capacity += (int) $row->available_places;
            $reserved += (int) $row->reserved_places;
        }
        $free      = max(0, $capacity - $reserved);
        $occupancy = $capacity > 0 ? round($reserved / $capacity * 100) : 0;

        ob_start();
        ?>
<div id="stat-panel">
    <div class="ls-stats">
        <div class="ls-stat ls-stat--lead">
            <span class="ls-stat__label">Occupancy</span>
            <span class="ls-stat__num"><?= $occupancy ?><small>%</small></span>
            <span class="ls-stat__bar"><i style="width:<?= min(100, $occupancy) ?>%"></i></span>
        </div>
        <div class="ls-stat"><span class="ls-stat__num"><?= $sessions ?></span><span class="ls-stat__label">Upcoming</span></div>
        <div class="ls-stat ls-stat--today"><span class="ls-stat__num"><?= $today ?></span><span class="ls-stat__label">Today</span></div>
        <div class="ls-stat ls-stat--free"><span class="ls-stat__num"><?= $free ?></span><span class="ls-stat__label">Free places</span></div>
        <div class="ls-stat ls-stat--res"><span class="ls-stat__num"><?= $reserved ?></span><span class="ls-stat__label">Reserved</span></div>
    </div>
</div>
        <?php
        return ob_get_clean();
    }
}

if (!function_exists('ls_render_board')) {
    function ls_render_board(array $rows): string {
        $today_str = date('Y-m-d');

        ob_start();
        if (empty($rows)) {
            ?>
<div class="ls-empty">
    <p class="ls-empty__title">No upcoming lessons</p>
    <p>Add a single lesson with &ldquo;New lesson&rdquo; or fill a date range with &ldquo;Bulk add&rdquo;.</p>
</div>
            <?php
            return ob_get_clean();
        }
        ?>
<div class="ls-board">
    <table class="ls-table">
        <thead>
            <tr>
                <th>Lesson</th>
                <th>Date</th>
                <th>Time</th>
                <th>Free</th>
                <th>Reserved</th>
                <th class="ls-th-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            $prev_date = null;
            foreach ($rows as $row):
                $is_today = ($row->date === $today_str);
                $free     = (int) $row->available_places - (int) $row->reserved_places;
                $new_day  = ($prev_date !== null && $row->date !== $prev_date);
                $prev_date = $row->date;
                $ts = strtotime($row->date);
            ?>
            <tr style="--i:<?= $i++ ?>" class="<?= $new_day ? 'ls-row--newday' : '' ?>">
                <td><span class="ls-name"><?= out($row->name) ?></span></td>
                <td>
                    <span class="ls-date<?= $is_today ? ' ls-date--today' : '' ?>">
                        <?= date('D, M j', $ts) ?>
                        <?php if ($is_today): ?><b>Today</b><?php endif; ?>
                    </span>
                </td>
                <td><span class="ls-time"><?= out(date('H:i', strtotime($row->start_time))) ?></span></td>
                <td>
                    <?php if ($free > 0): ?>
                        <span class="ls-free"><?= $free ?></span>
                    <?php else: ?>
                        <span class="ls-full">Full</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a class="ls-res<?= ((int) $row->reserved_places > 0) ? ' ls-res--has' : '' ?>"
                       title="View registrations"
                       mx-get="lessons-registrations/fetch/<?= $row->id ?>"
                       mx-build-modal='{"id": "registration-modal","modalHeading": "Lesson Registrations"}'><?= out($row->reserved_places) ?></a>
                </td>
                <td>
                    <div class="ls-actions">
                        <button type="button" class="ls-iconbtn" title="Edit schedule" aria-label="Edit schedule <?= (int) $row->id ?>"
                                mx-get="lessons-schedules/lesson_form/<?= $row->id ?>"
                                mx-build-modal='{"id": "lesson-modal","modalHeading": "Edit Lesson Schedule"}'>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                        </button>
                        <button type="button" class="ls-iconbtn ls-iconbtn--danger" title="Delete schedule" aria-label="Delete schedule <?= (int) $row->id ?>"
                                mx-post="lessons-schedules/delete_lesson/<?= $row->id ?>"
                                mx-on-success="#lessons-container"
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
