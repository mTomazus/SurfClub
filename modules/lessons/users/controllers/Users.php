<?php
class Users extends Trongate {

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
            $data['headline'] = 'Update User Record';
            $data['cancel_url'] = BASE_URL.'lessons-users/show/'.$update_id;
        } else {
            $data['headline'] = 'Create Lesson User Record';
            $data['cancel_url'] = BASE_URL.'lessons-users/manage';
        }

        $data['form_location'] = BASE_URL.'lessons-users/submit/'.$update_id;
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
            $params['email'] = '%'.$searchphrase.'%';
            $params['phone'] = '%'.$searchphrase.'%';
            $sql = 'select * from lesson_users
            WHERE name LIKE :name
            OR email LIKE :email
            OR phone LIKE :phone
            ORDER BY id desc';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Lesson Users';
            $all_rows = $this->model->get('id desc', 'lesson_users');
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->get_limit();
        $pagination_data['pagination_root'] = 'lessons-users/manage';
        $pagination_data['record_name_plural'] = 'lesson_users';
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
            redirect('lessons-users/manage');
        }

        $data = $this->get_data_from_db($update_id);
        $data['token'] = $token;

        if ($data === false) {
            redirect('lessons-users/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['headline'] = 'User Information';
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

        if ($submit === 'Apmokėti') {

            $this->validation->set_rules('name', 'name', 'required|min_length[2]|max_length[255]');
            $this->validation->set_rules('email', 'email', 'required|min_length[7]|max_length[255]|valid_email');
            $this->validation->set_rules('phone', 'phone', 'required|min_length[2]|max_length[255]');
            $this->validation->set_rules('registration_date', 'registration_date', 'required');
            $this->validation->set_rules('lesson_id', 'lesson_id', 'required');

            $result = $this->validation->run();

            if ($result === true) {

                $update_id = (int) segment(3);
                $data = $this->get_data_from_post();
                $data['registration_date'] = str_replace(' at ', '', $data['registration_date']);
                $data['registration_date'] = date('Y-m-d H:i', strtotime($data['registration_date']));

                if ($update_id>0) {
                    // update an existing record
                    $this->model->update($update_id, $data, 'lesson_users');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    // insert the new record
                    $update_id = $this->model->insert($data, 'lesson_users');
                    $flash_msg = 'The record was successfully created';
                }

                $reg_data['user_id'] = $update_id;
                $reg_data['schedule_id'] = post('lesson_id');
                $reg_data['registration_date'] = date('Y-m-d H:i:s');          
                $registration_id = $this->model->insert($reg_data, 'lesson_registrations');

                // Increment reserved_places in lesson_schedules
                $schedule_id = post('lesson_id');
                $sql = "UPDATE lesson_schedules SET reserved_places = reserved_places + 1 WHERE id = ?";
                $this->model->query_bind($sql, [$schedule_id]);

                set_flashdata($flash_msg);
                redirect('lessons-registrations/show/'.$registration_id);

            } else {
                //form submission error
                echo validation_errors();
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
            $params['module'] = 'lesson_users';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'lesson_users');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('lessons-users/manage');
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
        redirect('lessons-users/manage');
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
        $record_obj = $this->model->get_where($update_id, 'lesson_users');

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
        $data['email'] = post('email', true);
        $data['phone'] = post('phone', true);
        $data['registration_date'] = post('registration_date', true);        
        return $data;
    }

}