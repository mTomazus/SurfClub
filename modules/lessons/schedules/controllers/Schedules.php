<?php
class Schedules extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100);    

    /**
     * Display a all sheduled lessons .
    */
    public function index() {
        $sql = 'SELECT ls.id AS id, l.name, l.description, l.price, ls.date, ls.start_time, available_places, reserved_places  FROM lesson_schedules AS ls JOIN lessons AS l ON ls.lesson_id = l.id';
        $rows = $this->model->query($sql, 'object');
        $data['headline'] = 'Available Lessons';
        $data['rows'] = $rows;
        $data['view_file'] = 'index';
        $this->template('public', $data);
    }

    public function lessons() {
        $data['view_file'] = 'lessons_index';
        $data['rows'] = $this->model->get('date', 'lesson_schedules');
        $this->template('admin_area', $data);
    }

    public function fetch_lessons() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();
    	$data['rows'] = $this->model->get('id', 'lesson_schedules');
    	$this->view('lessons_table', $data);
    }

    public function lesson_form() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $update_id = (int) segment(3);
        $submit = post('submit');

        if (($submit === '') && ($update_id>0)) {
            $data = $this->get_data_from_db($update_id);
        } else {
            $data = $this->get_data_from_post();
        }

        $data['lessons'] = $this->model->get('id', 'lessons');

        if (from_trongate_mx() === true) {
            $this->view('lesson_form', $data);
        } else {
            $data['view_file'] = 'lesson_form';
            $this->template('admin_area', $data);
        }
    }

    public function delete_lesson() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

    	$update_id = segment(3, 'int');
    	$record_obj = $this->model->get_where($update_id, 'lesson_schedules');
    	$lesson_date = $record_obj->date ?? 'The';
    	$lesson_time = $record_obj->start_time ?? 'record';
    	
    	$this->model->delete($update_id, 'lesson_schedules');

    	$num_rows = $this->model->count('lesson_schedules');
    	if ($num_rows === 0) {
    		$sql = 'TRUNCATE TABLE lesson_schedules';
    		$this->model->query($sql);
    	}

    	http_response_code(200);
    	echo '<p>Lesson' . $update_id . ' dated ' . $lesson_date . ' ' . $lesson_time . ' was successfully deleted from the database.</p>';
    }

    /**
     * Display a webpage with a form for creating or updating a record.
     */
    public function create(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $update_id = (int) segment(3);
        $submit = post('submit');

        if (($submit === '') && ($update_id>0)) {
            $data = $this->get_data_from_db($update_id);
        } else {
            $data = $this->get_data_from_post();
        }

        $data['lessons_options'] = $this->_get_lessons_options($data['lesson_id']);

        if ($update_id>0) {
            $data['headline'] = 'Update Lesson Schedule Record';
            $data['cancel_url'] = BASE_URL.'lessons-schedules/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Lesson Schedule Record';
            $data['cancel_url'] = BASE_URL.'lessons-schedules/manage';
        }
        //----------------------------
        $sql = 'SELECT id, name from lessons ORDER BY id';
        $lessons = $this->model->query($sql, 'object');
        $data['lessons'] = $lessons;
        //-----------------------------
        $data['form_location'] = BASE_URL.'lessons-schedules/submit/'.$update_id;
        $data['view_file'] = 'create';
        $this->template('admin', $data);
    }

    /**
     * Display a webpage to manage records.
     *
     * @return void
     */
    public function manage(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if (segment(4) !== '') {
            $data['headline'] = 'Search Results';
            $searchphrase = trim($_GET['searchphrase']);
            $sql = 'select * from lesson_schedules ORDER BY id';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Lesson Schedules';
            $sql = 'SELECT ls.id AS id, l.name, l.description, l.price, ls.date, ls.start_time, available_places, reserved_places  FROM lesson_schedules AS ls JOIN lessons AS l ON ls.lesson_id = l.id';
            $all_rows = $this->model->query($sql, 'object');
    
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->get_limit();
        $pagination_data['pagination_root'] = 'lessons-schedules/manage';
        $pagination_data['record_name_plural'] = 'lesson_schedules';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->reduce_rows($all_rows);
        $data['selected_per_page'] = $this->get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_file'] = 'manage';
        $this->template('admin', $data);
    }

    /**
     * Display a webpage showing information for an individual record.
     *
     * @return void
     */
    public function show(): void {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();
        $update_id = (int) segment(3);

        if ($update_id === 0) {
            redirect('lessons-schedules/manage');
        }

        $data = $this->get_data_from_db($update_id);
        $data['token'] = $token;

        if ($data === false) {
            redirect('lessons-schedules/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['headline'] = 'Lesson Schedule Information';
            $data['view_file'] = 'show';
            $this->template('admin', $data);
        }
    }

    public function checkout_form(): void {

        $update_id = (int) segment(3);

        if ($update_id === 0) {
            redirect('lessons-schedules/manage');
        }
        $data = $this->get_data_from_db($update_id);

        $data['lessons'] = $this->model->get_one_where('id', $data['lesson_id'], 'lessons');
        
        if ($data === false) {
            redirect('lessons-schedules');
        } else {
            $data['update_id'] = $update_id;
            $data['view_file'] = 'checkout_form';
            $this->template('public', $data);
        }
    }

    /**
     * Handle submitted record data.
     *
     * @return void
     */
    public function submit(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $this->validation->set_rules('lesson_id', 'lesson_id', 'required|max_length[11]|numeric|greater_than[0]|integer');
        $this->validation->set_rules('date', 'date', 'required');
        $this->validation->set_rules('start_time', 'start_time', 'required|valid_time');      
        $this->validation->set_rules('available_places', 'available_places', 'required|max_length[11]|numeric|greater_than[0]|integer');
        $this->validation->set_rules('reserved_places', 'reserved_places', 'required|max_length[11]|numeric|integer');

        $result = $this->validation->run();

        if ($result === true) {

            $update_id = (int) segment(3);
            $data = $this->get_data_from_post();
            $data['date'] = date('Y-m-d', strtotime($data['date']));
            
            if ($update_id>0) {
                //update an existing record
                $this->model->update($update_id, $data, 'lesson_schedules');
                $flash_msg = 'The record was successfully updated';
            } else {
                //insert the new record
                $update_id = $this->model->insert($data, 'lesson_schedules');
                $flash_msg = 'The record was successfully created';
            }

            set_flashdata($flash_msg);
            if (from_trongate_mx() === false) {
                //if this is an MX request, we will return a success message
                redirect('lessons-schedules/manage');
            } 

        } else {
            echo 'Validation failed.';
            echo validation_error(400);
        }

    }

    /**
     * Handle submitted request for deletion.
     *
     * @return void
     */
    public function submit_delete(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit');
        $params['update_id'] = (int) segment(3);

        if (($submit === 'Yes - Delete Now') && ($params['update_id']>0)) {
            //delete all of the comments associated with this record
            $sql = 'delete from trongate_comments where target_table = :module and update_id = :update_id';
            $params['module'] = 'lesson_schedules';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'lesson_schedules');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            if (from_trongate_mx() === false) {
                //if this is an MX request, we will return a success message
                redirect('lessons-schedules/manage');
            } 
        }
    }

    /**
     * Set the number of items per page.
     *
     * @param int $selected_index Selected index for items per page.
     *
     * @return void
     */
    public function set_per_page(int $selected_index): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if (!is_numeric($selected_index)) {
            $selected_index = $this->per_page_options[1];
        }

        $_SESSION['selected_per_page'] = $selected_index;
        redirect('lessons-schedules/manage');
    }

    /**
     * Get the selected number of items per page.
     *
     * @return int Selected items per page.
     */
    private function get_selected_per_page(): int {
        $selected_per_page = (isset($_SESSION['selected_per_page'])) ? $_SESSION['selected_per_page'] : 1;
        return $selected_per_page;
    }

    /**
     * Reduce fetched table rows based on offset and limit.
     *
     * @param array $all_rows All rows to be reduced.
     *
     * @return array Reduced rows.
     */
    private function reduce_rows(array $all_rows): array {
        $rows = [];
        $start_index = $this->get_offset();
        $limit = $this->get_limit();
        $end_index = $start_index + $limit;

        $count = -1;
        foreach ($all_rows as $row) {
            $count++;
            if (($count>=$start_index) && ($count<$end_index)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * Get the limit for pagination.
     *
     * @return int Limit for pagination.
     */
    private function get_limit(): int {
        if (isset($_SESSION['selected_per_page'])) {
            $limit = $this->per_page_options[$_SESSION['selected_per_page']];
        } else {
            $limit = $this->default_limit;
        }

        return $limit;
    }

    /**
     * Get the offset for pagination.
     *
     * @return int Offset for pagination.
     */
    private function get_offset(): int {
        $page_num = (int) segment(3);

        if ($page_num>1) {
            $offset = ($page_num-1)*$this->get_limit();
        } else {
            $offset = 0;
        }

        return $offset;
    }

    /**
     * Get data from the database for a specific update_id.
     *
     * @param int $update_id The ID of the record to retrieve.
     *
     * @return array|null An array of data or null if the record doesn't exist.
     */
    private function get_data_from_db(int $update_id): ?array {
        $record_obj = $this->model->get_where($update_id, 'lesson_schedules');

        if ($record_obj === false) {
            $this->template('error_404');
            die();
        } else {
            $data = (array) $record_obj;
            return $data;        
        }
    }

    /**
     * Get data from the POST request.
     *
     * @return array Data from the POST request.
     */
    private function get_data_from_post(): array {
        $data['lesson_id'] = post('lesson_id', true);
        $data['date'] = post('date', true);
        $data['start_time'] = post('start_time', true);
        $data['available_places'] = post('available_places', true);
        $data['reserved_places'] = post('reserved_places', true);        
        return $data;
    }

    function _get_lessons_options($selected_key) {
        $this->module('module_relations');
        $options = $this->module_relations->_fetch_options($selected_key, 'lesson_schedules', 'lessons');
        return $options;
    }
}