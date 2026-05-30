<?php
class Staff_calendar extends Trongate {

    // Season is fixed: June (6) through August (8)
    private const SEASON_START_MONTH = 6;
    private const SEASON_END_MONTH   = 8;
    private const SEASON_YEAR        = 2026;

    // -------------------------------------------------------
    // INDEX — renders the monthly grid
    // -------------------------------------------------------
    public function index($month = null): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $month = $this->clamp_month((int) $month);

        $year       = self::SEASON_YEAR;
        $days_in_month = (int) date('t', mktime(0, 0, 0, $month, 1, $year));
        $month_start   = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
        $month_end     = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . $days_in_month;

        $staff   = $this->get_staff();
        $schedule = $this->get_schedule_for_month($month_start, $month_end);

        // Index schedule rows by "member_id|date" for O(1) cell lookup in the view
        $schedule_index = [];
        foreach ($schedule as $row) {
            $key = $row->member_id . '|' . $row->schedule_date;
            $schedule_index[$key] = $row;
        }

        $data['staff']           = $staff;
        $data['schedule_index']  = $schedule_index;
        $data['month']           = $month;
        $data['year']            = $year;
        $data['days_in_month']   = $days_in_month;
        $data['month_name']      = $this->month_name($month);
        $data['prev_month']      = ($month > self::SEASON_START_MONTH) ? $month - 1 : null;
        $data['next_month']      = ($month < self::SEASON_END_MONTH)   ? $month + 1 : null;
        $data['view_file']       = 'calendar';

        $this->template('admin_area', $data);
    }

    // -------------------------------------------------------
    // UPDATE_STATUS — AJAX POST endpoint, upserts one cell
    // -------------------------------------------------------
    public function update_status(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        header('Content-Type: application/json');

        $member_id = (int) post('member_id', true);
        $date      = post('date', true);
        $status    = post('status', true);
        $notes     = post('notes', true);

        // Validate inputs before touching the database
        if ($member_id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid member_id']);
            return;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            echo json_encode(['success' => false, 'error' => 'Invalid date format']);
            return;
        }

        $allowed_statuses = ['working', 'halfday', 'dayoff', 'sick', ''];
        if (!in_array($status, $allowed_statuses, true)) {
            echo json_encode(['success' => false, 'error' => 'Invalid status']);
            return;
        }

        // Confirm the member actually exists (prevents phantom inserts)
        $member_check_sql = 'SELECT id FROM members WHERE id = ?';
        $exists = $this->model->query_bind($member_check_sql, [$member_id], 'object');
        if (empty($exists)) {
            echo json_encode(['success' => false, 'error' => 'Member not found']);
            return;
        }

        if ($status === '') {
            // "Clear" — delete the row entirely so the cell goes back to white
            $sql    = 'DELETE FROM staff_schedule WHERE member_id = ? AND schedule_date = ?';
            $params = [$member_id, $date];
            $this->model->query_bind($sql, $params);
        } else {
            // INSERT … ON DUPLICATE KEY UPDATE (atomic upsert, safe with the UNIQUE key)
            $sql = 'INSERT INTO staff_schedule (member_id, schedule_date, status, notes)
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        status     = VALUES(status),
                        notes      = VALUES(notes),
                        updated_at = CURRENT_TIMESTAMP';

            $params = [$member_id, $date, $status, $notes];
            $this->model->query_bind($sql, $params);
        }

        echo json_encode(['success' => true]);
    }

    // -------------------------------------------------------
    // EXPORT_PDF — print-friendly page, triggers window.print()
    // -------------------------------------------------------
    public function export_pdf($month = null): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $month = $this->clamp_month((int) $month);

        $year            = self::SEASON_YEAR;
        $days_in_month   = (int) date('t', mktime(0, 0, 0, $month, 1, $year));
        $month_start     = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
        $month_end       = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . $days_in_month;

        $staff           = $this->get_staff();
        $schedule        = $this->get_schedule_for_month($month_start, $month_end);

        $schedule_index  = [];
        foreach ($schedule as $row) {
            $key = $row->member_id . '|' . $row->schedule_date;
            $schedule_index[$key] = $row;
        }

        $data['staff']          = $staff;
        $data['schedule_index'] = $schedule_index;
        $data['month']          = $month;
        $data['year']           = $year;
        $data['days_in_month']  = $days_in_month;
        $data['month_name']     = $this->month_name($month);

        // Render a standalone print view — no admin chrome
        $this->view('calendar_print', $data);
    }

    // -------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------

    private function get_staff(): array {
        $sql = "SELECT id, username, full_name, role FROM members
                WHERE full_name IS NOT NULL AND full_name != ''
                ORDER BY id ASC";
        $rows = $this->model->query($sql, 'object');
        return $rows ?: [];
    }

    private function get_schedule_for_month(string $start, string $end): array {
        $sql    = 'SELECT member_id, schedule_date, status, notes
                   FROM staff_schedule
                   WHERE schedule_date BETWEEN ? AND ?';
        $params = [$start, $end];
        $rows   = $this->model->query_bind($sql, $params, 'object');
        return $rows ?: [];
    }

    private function clamp_month(int $month): int {
        $current = (int) date('n');

        if ($month < self::SEASON_START_MONTH || $month > self::SEASON_END_MONTH) {
            // Default to current month if in season, otherwise June
            if ($current >= self::SEASON_START_MONTH && $current <= self::SEASON_END_MONTH) {
                return $current;
            }
            return self::SEASON_START_MONTH;
        }

        return $month;
    }

    private function month_name(int $month): string {
        $names = [
            6 => 'June',
            7 => 'July',
            8 => 'August',
        ];
        return $names[$month] ?? 'June';
    }
}
