<?php
class Camps extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100); 

    // ------------- SHOW ALL REGISTRATIONS -------------
    //---------------------------------------------------
    public function index(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $show_only = $this->get_show_only();

        $data['registrations'] = $this->get_all_registrations($show_only);

        $data['view_file'] = 'reservations';

        $this->template('admin_area', $data);
    }

    function get_show_only() {
        $show_only_val = segment(3); 

        switch($show_only_val) {
            case '':
                $show_only = '';
                break;
            case '1':
                $show_only = '1. 2026-06-08 - 2026-06-12';
                break;
            case '2':
                $show_only = '2. 2026-06-15 - 2026-06-19';
                break;
            case '3':
                $show_only = '3. 2026-06-22 - 2026-06-26';
                break;
            case '4':
                $show_only = '4. 2026-06-29 - 2026-07-03';
                break;
            case '5':
                $show_only = '5. 2026-07-06 - 2026-07-10';
                break;
            case '6':
                $show_only = '6. 2026-07-13 - 2026-07-17';
                break;
            case '7':
                $show_only = '7. 2026-07-20 - 2026-07-24';
                break;
            case '8':
                $show_only = '8. 2026-07-27 - 2026-07-31';
                break;
            case '9':
                $show_only = '9. 2026-08-03 - 2026-08-07';
                break;
            case '10':
                $show_only = '10. 2026-08-10 - 2026-08-14';
                break;
            case '11':
                $show_only = '11. 2026-08-17 - 2026-08-21';
                break;
            case '12':
                $show_only = '12. 2026-08-24 - 2026-08-28';
                break;
        }
        return $show_only;
    }
    
    private function get_all_registrations($show_only) {

        // Base SQL query
        $sql = "SELECT * FROM camps";
        // If $show_only is not empty, add the division filter
        if (!empty($show_only)) {
            $sql .= " WHERE pamaina = ?";
            $params = [$show_only];
            $sql .= " ORDER BY id";
            $registrations = $this->model->query_bind($sql, $params, 'object');
        } else {
            // Execute the query with bound parameters
            $registrations = $this->model->get('id desc', 'camps');
        }
        return $registrations;
    }

    /**           -----SOON----
    * Display a soon info instead of form.
    */
    public function soon(): void {

        $this->view('soon');

    }

    /**           -----CALLBACK & THANKS----
    * callback function.
    */
    public function thanks(): void {

        $order = $_GET['order_reference'];
        $payment = $_GET['payment_reference'];

        $data['payment_ref'] = $payment;
        $data['date_paid'] = date("Y-m-d h:m:s");
        $data['status'] = 'completed';

        $payment_success = $this->model->update_where("order_ref", $order, $data);

        $data['camp'] = $this->model->get_one_where("order_ref", $order, 'camps');

        $data['view_file'] = 'thanks';
        $this->template('public', $data);

    }

    /**           -----CURL_mail----
    * Sending mail thou brevo api on form registration.
    */    
    public function curl_mail(): void {

        $curl = curl_init();

        $name = post('name');
        $pamaina = post('pamaina');
        $phone = post('phone');
        $email = post('email');

        $url = $this->get_everypay_url();

        $data = [
            "sender" => [
                "name" => "VšĮ Banglentė",
                "email" => "sales@banglente.com"
            ],
            "to" => [
                [
                    "email" => $email,
                ]
            ],
            "subject" => "Surf Stovyklos Registracija",
            "templateId" => 32,
            "params" => [
                "name" => $name,
                "pamaina" => $pamaina,
                "phone" => $phone,
                "everypay_url" => $url
            ],
            "headers" => [
                "X-Mailin-custom" => "custom_header_1:custom_value_1|custom_header_2:custom_value_2|custom_header_3:custom_value_3|custom_header_4:custom_value_4",
                "charset" => "iso-8859-1"
            ]
        ];

        $api_secret = constant('BREVO_API_KEY');

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.brevo.com/v3/smtp/email",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "api-key: ".$api_secret,
                "content-type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
        ]);

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

    }

    /**           -----FORMA----
    * Sending mail thou brevo api on form registration.
    */  
    public function forma(): void {

        $update_id = (int) segment(3);
        $submit = post('submit');

        $rows = $this->model->get('id asc', 'camps_pamainos');
        $data['rows'] = $rows;

        $data['form_location'] = 'camps/submit2/'.$update_id;
        $this->view('form', $data);

    }

    /**           -----SUBMIT2----
    * Sending data from form to database and email.
    */  
    public function submit2(): void {

        $this->validation->set_rules('name', 'Name', 'required|min_length[5]|max_length[255]');
        $this->validation->set_rules('phone', 'Phone', 'required|min_length[5]|max_length[14]');
        $this->validation->set_rules('email', 'Email', 'required|min_length[5]|max_length[35]|valid_email');
        $this->validation->set_rules('pamaina', 'Pamaina', 'required|max_length[45]');
        $this->validation->set_rules('age', 'Age', 'required|integer|greater_than[6]|less_than[19]');
        $this->validation->set_rules('status', 'Status', 'required|min_length[2]|max_length[15]');
        $this->validation->set_rules('reference', 'Reference', 'required|exact_length[16]');

        $result = $this->validation->run();

        if ($result === true) {

            $reference = post('reference');
            $url = $this->get_everypay_url();

            $update_id = (int) segment(3);
            $data = $this->get_data_from_post();

            $data['everypay_url'] = $url;

            $update_id = $this->model->insert($data, 'camps');

            $flash_msg = 'The record was successfully created';

            set_flashdata($flash_msg);

            $mail = $this->curl_mail();

            $this->view('payment', $data);

        } else {
            http_response_code(400);
            echo validation_errors(400);
        }

    }

    /**
     * Make Everypay link from the POST request.
     *
     * @return payment url from the POST request.
     */
    private function get_everypay_url() {
        $linkpay_prefix = constant('EVERYPAY_PAYLINK'); // LinkPay URL
        $api_secret = constant('EVERYPAY_AUTH_KEY'); //API SECRET FROM GENERAL SETTINGS
        $params = [ //BELOW ADD ALL PARAMETERS WHICH YOU ENABLED UNDER LINKPAY LINK AND DATA
        'customer_name' => post('name'), // VARDAS PAVARDE
        'custom_field_2' => post('pamaina'), // STOVYKLOS PAMAINA
        'order_reference' => post('reference'), //ORDER REFERENCE
        'linkpay_token' => constant('EVERYPAY_PAYLINK_TOKEN') //LINKPAY TOKEN FROM THE LINK
        ];
        
        $query = http_build_query($params, null, '&', PHP_QUERY_RFC3986);

        $hmac = hash_hmac('sha256', $query, $api_secret);
        $url = "${linkpay_prefix}?${query}&hmac=${hmac}";

        return $url;
    }

    /**
     * Display a webpage with a payment link and info.
     */    
    public function payment(): void {

        $update_id = (int) segment(3);

        $data = $this->get_data_from_db($update_id);

        $this->view('payment', $data);

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
            $data['headline'] = 'Update Camp Record';
            $data['cancel_url'] = BASE_URL.'camps/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Camp Record';
            $data['cancel_url'] = BASE_URL.'camps/manage';
        }

        $data['form_location'] = BASE_URL.'camps/submit/'.$update_id;
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
            $params['phone'] = '%'.$searchphrase.'%';
            $params['email'] = '%'.$searchphrase.'%';
            $params['pamaina'] = '%'.$searchphrase.'%';
            $params['age'] = '%'.$searchphrase.'%';
            $params['status'] = '%'.$searchphrase.'%';
            $sql = 'select * from camps
            WHERE name LIKE :name
            OR phone LIKE :phone
            OR email LIKE :email
            OR pamaina LIKE :pamaina
            OR age LIKE :age
            OR status LIKE :status
            ORDER BY id desc';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Camps';
            $all_rows = $this->model->get('id desc');
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->get_limit();
        $pagination_data['pagination_root'] = 'camps/manage';
        $pagination_data['record_name_plural'] = 'camps';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->reduce_rows($all_rows);
        $data['selected_per_page'] = $this->get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'camps';
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
            redirect('camps/manage');
        }

        $data = $this->get_data_from_db($update_id);
        $data['token'] = $token;

        if ($data === false) {
            redirect('camps/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['headline'] = 'Camp Information';
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

            $this->validation->set_rules('name', 'Name', 'required|min_length[2]|max_length[255]');
            $this->validation->set_rules('phone', 'Phone', 'required|min_length[2]|max_length[255]');
            $this->validation->set_rules('email', 'Email', 'required|min_length[7]|max_length[255]|valid_email');
            $this->validation->set_rules('pamaina', 'Pamaina', 'required|min_length[2]|max_length[255]');
            $this->validation->set_rules('age', 'Age', 'required|min_length[2]|max_length[255]');
            $this->validation->set_rules('status', 'Status', 'required|min_length[2]|max_length[255]');

            $result = $this->validation->run();

            if ($result === true) {

                $update_id = (int) segment(3);
                $data = $this->get_data_from_post();
                
                if ($update_id>0) {
                    //update an existing record
                    $this->model->update($update_id, $data, 'camps');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    //insert the new record
                    $update_id = $this->model->insert($data, 'camps');
                    $flash_msg = 'The record was successfully created';
                }

                set_flashdata($flash_msg);
                redirect('camps/show/'.$update_id);

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
            $params['module'] = 'camps';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'camps');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('camps/manage');
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
        redirect('camps/manage');
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
        $record_obj = $this->model->get_where($update_id, 'camps');

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
        $data['phone'] = post('phone', true);
        $data['email'] = post('email', true);
        $data['pamaina'] = post('pamaina', true);
        $data['age'] = post('age', true);
        $data['status'] = post('status', true);        
        $data['order_ref'] = post('reference', true);        
        return $data;
    }

}