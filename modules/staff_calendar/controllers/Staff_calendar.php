<?php
class Staff_calendar extends Trongate {

    // Season is fixed: June (6) through August (8)
    private const SEASON_START_MONTH = 6;
    private const SEASON_END_MONTH   = 8;
    private const SEASON_YEAR        = 2026;

    private const ALLOWED_STATUSES = ['working', 'halfday', 'dayoff', 'sick', ''];

    // -------------------------------------------------------
    // INDEX — renders the monthly grid
    // -------------------------------------------------------
    public function index($month = null): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $month         = $this->clamp_month((int) $month);
        $year          = self::SEASON_YEAR;
        $days_in_month = (int) date('t', mktime(0, 0, 0, $month, 1, $year));

        [$month_start, $month_end] = $this->month_bounds($month, $year);

        $staff          = $this->get_staff();
        $schedule_index = $this->get_schedule_index($month_start, $month_end);

        $data['title']          = 'Staff Calendar';
        $data['staff']          = $staff;
        $data['schedule_index'] = $schedule_index;
        $data['stats']          = $this->compute_stats($schedule_index, count($staff), $days_in_month);
        $data['month']          = $month;
        $data['year']           = $year;
        $data['days_in_month']  = $days_in_month;
        $data['month_name']     = $this->month_name($month);
        $data['prev_month']     = ($month > self::SEASON_START_MONTH) ? $month - 1 : null;
        $data['next_month']     = ($month < self::SEASON_END_MONTH)   ? $month + 1 : null;
        $data['view_file']      = 'calendar';

        $this->template('admin_area', $data);
    }

    // -------------------------------------------------------
    // SET_STATUS — MX POST endpoint.
    // URL: staff_calendar/set_status/{member_id}/{YYYY-MM-DD}
    // Echoes HTML fragments: the cell, the row-total cell, the
    // stat panel — picked up by mx-select + mx-select-oob.
    // -------------------------------------------------------
    public function set_status(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $member_id = (int) segment(3);
        $date      = segment(4);
        $status    = (string) post('status');
        $notes     = (string) post('notes');

        // Validate before touching the database
        if ($member_id <= 0
            || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)
            || !in_array($status, self::ALLOWED_STATUSES, true)) {
            http_response_code(400);
            echo '<td class="sc-cell"><div class="sc-quick">!</div></td>';
            return;
        }

        // Confirm the member exists (prevents phantom inserts)
        $exists = $this->model->get_where($member_id, 'members');
        if (!$exists) {
            http_response_code(404);
            echo '<td class="sc-cell"><div class="sc-quick">?</div></td>';
            return;
        }

        if ($status === '') {
            $sql = 'DELETE FROM staff_schedule WHERE member_id = ? AND schedule_date = ?';
            $this->model->query_bind($sql, [$member_id, $date]);
        } else {
            $sql = 'INSERT INTO staff_schedule (member_id, schedule_date, status, notes)
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        status     = VALUES(status),
                        notes      = VALUES(notes),
                        updated_at = CURRENT_TIMESTAMP';
            $this->model->query_bind($sql, [$member_id, $date, $status, $notes]);
        }

        $this->emit_cell_response($member_id, $date, $status, $notes);
    }

    // -------------------------------------------------------
    // CELL_FORM — MX GET endpoint, returns modal body.
    // URL: staff_calendar/cell_form/{member_id}/{YYYY-MM-DD}
    // -------------------------------------------------------
    public function cell_form(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $member_id = (int) segment(3);
        $date      = segment(4);

        if ($member_id <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            echo '<p>Invalid request.</p>';
            return;
        }

        $member = $this->model->get_where($member_id, 'members');
        $entry  = $this->fetch_entry($member_id, $date);

        $data['member_id'] = $member_id;
        $data['date_str']  = $date;
        $data['member']    = $member;
        $data['status']    = $entry->status ?? '';
        $data['notes']     = $entry->notes  ?? '';

        $this->view('_cell_form', $data);
    }

    // -------------------------------------------------------
    // EXPORT_PDF — print-friendly page, triggers window.print()
    // -------------------------------------------------------
    public function export_pdf($month = null): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $month         = $this->clamp_month((int) $month);
        $year          = self::SEASON_YEAR;
        $days_in_month = (int) date('t', mktime(0, 0, 0, $month, 1, $year));

        [$month_start, $month_end] = $this->month_bounds($month, $year);

        $data['staff']          = $this->get_staff();
        $data['schedule_index'] = $this->get_schedule_index($month_start, $month_end);
        $data['month']          = $month;
        $data['year']           = $year;
        $data['days_in_month']  = $days_in_month;
        $data['month_name']     = $this->month_name($month);

        $this->view('calendar_print', $data);
    }

    // -------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------

    /**
     * Echo the fragments an MX cell update needs:
     *   - the rebuilt cell <td>          (primary target, mx-select)
     *   - the recomputed row-total <td>  (OOB)
     *   - the recomputed stat panel      (OOB)
     */
    private function emit_cell_response(int $member_id, string $date, string $status, string $notes): void {
        require_once APPPATH . 'modules/staff_calendar/views/_sc_helpers.php';

        $month = (int) substr($date, 5, 2);
        $year  = (int) substr($date, 0, 4);
        $days_in_month = (int) date('t', mktime(0, 0, 0, $month, 1, $year));
        [$month_start, $month_end] = $this->month_bounds($month, $year);

        $schedule_index = $this->get_schedule_index($month_start, $month_end);

        // The member's row total
        $row_total = 0.0;
        foreach ($schedule_index as $entry) {
            if ((int) $entry->member_id !== $member_id) continue;
            if ($entry->status === 'working') $row_total += 1.0;
            if ($entry->status === 'halfday') $row_total += 0.5;
        }

        $dow = (int) date('N', strtotime($date));
        $cell_html = sc_render_cell([
            'member_id' => $member_id,
            'date_str'  => $date,
            'status'    => $status,
            'notes'     => $notes,
            'is_wkend'  => $dow >= 6,
        ]);

        // The cell + row-total are <td>s — wrap them in a full table so they
        // survive being parsed via div.innerHTML on the client (bare <td>s get
        // stripped). mx-select / mx-select-oob extract them by id.
        echo '<table><tbody><tr>';
        echo $cell_html;
        echo sc_render_row_total($member_id, $row_total);
        echo '</tr></tbody></table>';

        // The stat panel (OOB swap uses innerHTML, so the wrapper #stat-panel stays put)
        $staff_count = count($this->get_staff());
        echo sc_render_stat_panel($this->compute_stats($schedule_index, $staff_count, $days_in_month));
    }

    private function get_staff(): array {
        $sql = "SELECT id, username, full_name, role FROM members
                WHERE full_name IS NOT NULL AND full_name != ''
                ORDER BY id ASC";
        return $this->model->query($sql, 'object') ?: [];
    }

    /** Build a "member_id|date" => row lookup for O(1) cell access. */
    private function get_schedule_index(string $start, string $end): array {
        $sql    = 'SELECT member_id, schedule_date, status, notes
                   FROM staff_schedule
                   WHERE schedule_date BETWEEN ? AND ?';
        $rows   = $this->model->query_bind($sql, [$start, $end], 'object') ?: [];

        $index = [];
        foreach ($rows as $row) {
            $index[$row->member_id . '|' . $row->schedule_date] = $row;
        }
        return $index;
    }

    private function fetch_entry(int $member_id, string $date) {
        $rows = $this->model->query_bind(
            'SELECT member_id, schedule_date, status, notes FROM staff_schedule
             WHERE member_id = ? AND schedule_date = ?',
            [$member_id, $date], 'object'
        );
        return $rows[0] ?? null;
    }

    /** Tally the four status buckets + coverage % across the whole month. */
    private function compute_stats(array $schedule_index, int $total_staff, int $days_in_month): array {
        $counts = ['working' => 0, 'halfday' => 0, 'dayoff' => 0, 'sick' => 0];
        foreach ($schedule_index as $entry) {
            if (isset($counts[$entry->status])) $counts[$entry->status]++;
        }
        $filled      = array_sum($counts);
        $total_slots = $total_staff * $days_in_month;

        return [
            'total_staff' => $total_staff,
            'working'     => $counts['working'],
            'halfday'     => $counts['halfday'],
            'dayoff'      => $counts['dayoff'],
            'sick'        => $counts['sick'],
            'coverage'    => $total_slots > 0 ? round($filled / $total_slots * 100) : 0,
        ];
    }

    private function month_bounds(int $month, int $year): array {
        $days  = (int) date('t', mktime(0, 0, 0, $month, 1, $year));
        $mm    = str_pad((string) $month, 2, '0', STR_PAD_LEFT);
        return ["$year-$mm-01", "$year-$mm-" . str_pad((string) $days, 2, '0', STR_PAD_LEFT)];
    }

    private function clamp_month(int $month): int {
        $current = (int) date('n');
        if ($month < self::SEASON_START_MONTH || $month > self::SEASON_END_MONTH) {
            if ($current >= self::SEASON_START_MONTH && $current <= self::SEASON_END_MONTH) {
                return $current;
            }
            return self::SEASON_START_MONTH;
        }
        return $month;
    }

    private function month_name(int $month): string {
        return [6 => 'June', 7 => 'July', 8 => 'August'][$month] ?? 'June';
    }
}
