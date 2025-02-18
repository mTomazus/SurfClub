<?php
class Lessons extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100);    

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

        if ($update_id>0) {
            $data['headline'] = 'Update Lesson Record';
            $data['cancel_url'] = BASE_URL.'lessons/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Lesson Record';
            $data['cancel_url'] = BASE_URL.'lessons/manage';
        }

        $data['form_location'] = BASE_URL.'lessons/submit/'.$update_id;
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
            $params['name'] = '%'.$searchphrase.'%';
            $sql = 'select * from lessons
            WHERE name LIKE :name
            ORDER BY id';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Lessons';
            $all_rows = $this->model->get('id');
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->get_limit();
        $pagination_data['pagination_root'] = 'lessons/manage';
        $pagination_data['record_name_plural'] = 'lessons';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->reduce_rows($all_rows);
        $data['selected_per_page'] = $this->get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'lessons';
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
            redirect('lessons/manage');
        }

        $data = $this->get_data_from_db($update_id);
        $data['token'] = $token;

        if ($data === false) {
            redirect('lessons/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['headline'] = 'Lesson Information';
            $data['view_file'] = 'show';
            $this->template('admin', $data);
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

        $submit = post('submit', true);

        if ($submit === 'Submit') {

            $this->validation->set_rules('name', 'name', 'required|min_length[2]|max_length[255]');
            $this->validation->set_rules('description', 'description', 'required|min_length[2]');

            $result = $this->validation->run();

            if ($result === true) {

                $update_id = (int) segment(3);
                $data = $this->get_data_from_post();
                
                if ($update_id>0) {
                    //update an existing record
                    $this->model->update($update_id, $data, 'lessons');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    //insert the new record
                    $update_id = $this->model->insert($data, 'lessons');
                    $flash_msg = 'The record was successfully created';
                }

                set_flashdata($flash_msg);
                redirect('lessons/show/'.$update_id);

            } else {
                //form submission error
                $this->create();
            }

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
            $params['module'] = 'lessons';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'lessons');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('lessons/manage');
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
        redirect('lessons/manage');
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
        $record_obj = $this->model->get_where($update_id, 'lessons');

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
        $data['name'] = post('name', true);
        $data['description'] = post('description', true);        
        return $data;
    }

}