<?php
class Coupons extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100);    

    public function index() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();
        $data['rows'] = $this->model->get();
        $data['view_file'] = 'index_coupon';
        $this->template('admin_area', $data);
    }

    public function fetch_table() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $data['rows'] = $this->model->get();
        $this->view('fetch_table', $data);
    }

    public function coupon_form() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $update_id = (int) segment(3);
        if ($update_id>0) {
           $data = $this->get_data_from_db($update_id); 
           $this->view('coupon_form', $data);
        } else {
            $this->view('coupon_form');
        } 
    }

    public function delete_modal() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $update_id = (int) segment(3);
        $data['update_id'] = $update_id;
        $this->view('delete_modal', $data);
    }

    public function submit_coupon(): void{

        $this->validation->set_rules('coupon_type', 'Coupon Type', 'required|min_length[2]|max_length[255]');
        $this->validation->set_rules('price', 'Price', 'required|min_length[2]|max_length[255]');
        $this->validation->set_rules('phone', 'Phone', 'required|min_length[2]|max_length[255]');
        $this->validation->set_rules('name', 'Name', 'required|min_length[2]|max_length[255]');
        $this->validation->set_rules('email', 'Email', 'required|valid_email');

        $result = $this->validation->run();

        if ($result === true) {

            $update_id = (int) segment(3);
            $data = $this->get_data_from_post();
            
            if ($update_id>0) {
                //update an existing record
                $this->model->update($update_id, $data, 'coupons');
                $flash_msg = 'The coupon record was successfully updated';
            } else {
                //insert the new record
                $this->model->insert($data, 'coupons');
                $flash_msg = 'The coupon record was successfully created';
            }

            http_response_code(200);
            echo '<p>' . $flash_msg . '</p>';
                
        } else {
            //form submission error
            echo validation_errors();
            http_response_code(400); 
            $this->coupon_form();

        }
    }

    public function submit_delete_coupon() {
        $record_id = segment(3,'int');
        $this->model->delete($record_id);
        echo '<p>The coupon record was successfully deleted</p>';
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

        if ($update_id>0) {
            $data['headline'] = 'Update Coupon Record';
            $data['cancel_url'] = BASE_URL.'coupons/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Coupon Record';
            $data['cancel_url'] = BASE_URL.'coupons/manage';
        }

        $data['form_location'] = BASE_URL.'coupons/submit/'.$update_id;
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
            $params['coupon_type'] = '%'.$searchphrase.'%';
            $params['price'] = '%'.$searchphrase.'%';
            $params['phone'] = '%'.$searchphrase.'%';
            $params['name'] = '%'.$searchphrase.'%';
            $sql = 'select * from coupons
            WHERE coupon_type LIKE :coupon_type
            OR price LIKE :price
            OR phone LIKE :phone
            OR name LIKE :name
            ORDER BY date_formed desc';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Coupons';
            $all_rows = $this->model->get('date_formed desc');
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->get_limit();
        $pagination_data['pagination_root'] = 'coupons/manage';
        $pagination_data['record_name_plural'] = 'coupons';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->reduce_rows($all_rows);
        $data['selected_per_page'] = $this->get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'coupons';
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
            redirect('coupons/manage');
        }

        $data = $this->get_data_from_db($update_id);
        $data['token'] = $token;

        if ($data === false) {
            redirect('coupons/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['headline'] = 'Coupon Information';
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

            $this->validation->set_rules('coupon_type', 'Coupon Type', 'required|min_length[2]|max_length[255]');
            $this->validation->set_rules('price', 'Price', 'required|min_length[2]|max_length[255]');
            $this->validation->set_rules('phone', 'Phone', 'required|min_length[2]|max_length[255]');
            $this->validation->set_rules('name', 'Name', 'required|min_length[2]|max_length[255]');
            $this->validation->set_rules('email', 'Email', 'required|valid_email');

            $result = $this->validation->run();

            if ($result === true) {

                $update_id = (int) segment(3);
                $data = $this->get_data_from_post();
                
                if ($update_id>0) {
                    //update an existing record
                    $this->model->update($update_id, $data, 'coupons');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    //insert the new record
                    $update_id = $this->model->insert($data, 'coupons');
                    $flash_msg = 'The record was successfully created';
                }

                set_flashdata($flash_msg);
                redirect('coupons/show/'.$update_id);

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
            $params['module'] = 'coupons';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'coupons');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('coupons/manage');
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
        redirect('coupons/manage');
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
        $record_obj = $this->model->get_where($update_id, 'coupons');

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
        $data['coupon_type'] = post('coupon_type', true);
        $data['price'] = post('price', true);
        $data['phone'] = post('phone', true);
        $data['name'] = post('name', true);
        $data['email'] = post('email', true);       
        $data['status'] = post('status', true);       
        $data['date_formed'] = post('date_formed', true);       
        return $data;
    }

}