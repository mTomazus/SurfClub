<?php
class Registrations extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100);

    public function fetch() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $params['update_id'] = (int) segment(3);

        $sql = "SELECT lu.name, lu.email, lu.phone
                FROM lesson_registrations lr
                JOIN lesson_users lu ON lr.user_id = lu.id
                WHERE lr.schedule_id = :update_id
                AND lr.status = 'settled'
                ORDER BY lu.id";

        $data['rows'] = $this->model->query_bind($sql, $params, 'array');
        
        $this->view("fetch_table", $data);
    }

    public function process_order() {
        
        $this->validation->set_rules('customer_name', 'customer_name', 'required|min_length[4]|max_length[255]');
        $this->validation->set_rules('phone', 'phone', 'required|min_length[8]|max_length[15]');
        $this->validation->set_rules('email', 'email', 'required|valid_email');
        $result = $this->validation->run();

        if ($result === true) {

            $update_id = (int) post('lesson_id', true);

            // Confirm the chosen schedule exists, is still upcoming and has a free
            // place before creating any records or a payment link.
            $sql = "SELECT ls.available_places, ls.reserved_places, ls.date, l.price
                    FROM lesson_schedules ls
                    JOIN lessons l ON ls.lesson_id = l.id
                    WHERE ls.id = :update_id";
            $schedule = $this->model->query_bind($sql, ['update_id' => $update_id], 'array');

            if (empty($schedule)) {
                http_response_code(400);
                echo '<p>Pasirinkta pamoka nerasta.</p>';
                return;
            }

            $schedule = $schedule[0];

            if ($schedule['date'] < date('Y-m-d')) {
                http_response_code(400);
                echo '<p>Šios pamokos registracija nebegalima.</p>';
                return;
            }

            if ((int) $schedule['reserved_places'] >= (int) $schedule['available_places']) {
                http_response_code(400);
                echo '<p>Deja, šioje pamokoje nebėra laisvų vietų.</p>';
                return;
            }

            $total_amount = $schedule['price'];

            // insert the new customer record
            $data['name'] = post('customer_name', true);
            $data['phone'] = post('phone', true);
            $data['email'] = post('email', true);
            $data['registration_date'] = date('Y-m-d H:i');
            $user_id = $this->model->insert($data, 'lesson_users');

            // Store order details
            $reg_data['user_id'] = $user_id; // Assuming user_id is the same as customer_id
            $reg_data['schedule_id'] = $update_id; // Assuming schedule_id is the same as lesson_id
            $reg_data['registration_date'] = date('Y-m-d H:i');          
            $order_id = $this->model->insert($reg_data, 'lesson_registrations');

            // Generate payment link using EveryPay
            $payment_link = $this->_get_everypay_link($order_id, $total_amount);

            if ($payment_link) {

                // Store payment link in session or pass via redirect with order_id
                $_SESSION['payment_link'] = $payment_link;
                // redirect('products/payment/' . $order_id);
                http_response_code(200);
                
                echo '<div class="progress-bar active"><div class="steps active" data-title="informacija"></div>';
                echo '<div class="steps active" data-title="apmokėjimas"></div></div>';

                echo '<h3 class="text-center mb-1">Mokėjimo nuoroda</h3>';
                echo '<p class="text-center">Jūsų užsakymas buvo sėkmingai sukurtas. Spauskite žemiau esančią nuorodą, kad atliktumėte mokėjimą ir patvirtintumėte užsakymą.</p>';
                echo '<a class="button success mt-1 mb-1" style ="margin:auto;display:block;font-family:Baskerville;font-weight:600;" href="' . $payment_link . '">Apmokėti</a>';

            } else {
                http_response_code(400);
                echo "Payment initialization failed.";
            }

            return;
        }

        $msg = "Visi formos laukai turi būti užpildyti.";
        set_flashdata($msg);
        echo validation_errors(400);

    }

    function _get_everypay_link($order_id, $total_amount) {

        $api_username = constant('EVERYPAY_API_USERNAME');
        $account_name = constant('EVERYPAY_ACCOUNT_NAME');
        $auth_key = constant('EVERYPAY_AUTH_KEY');
        $api_url = constant('EVERYPAY_URL');

        $everypay_url = "{$api_url}oneoff";
    
        $amount = number_format($total_amount, 2, '.', '');
        $payload = [
            "api_username" => $api_username,
            "account_name" => $account_name,
            "amount" => $amount,
            "order_reference" => "ORDER-25-$order_id",
            "nonce" => uniqid(),
            "timestamp" => date('c'),
            "customer_url" => BASE_URL . "lessons-registrations/payment_result"
        ];
    
        $ch = curl_init($everypay_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . base64_encode($api_username . ':' . $auth_key),
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $response = curl_exec($ch);
        curl_close($ch);
    
        $result = json_decode($response, true);
    
        if (isset($result['payment_link'])) {
            return $result['payment_link'];
        } else {
            // Optional debug log
            // file_put_contents('everypay_errors.log', $response . PHP_EOL, FILE_APPEND);
            return false;
        }
    }

    function payment_result() {
        $payment_ref = $_GET['payment_reference'] ?? null;
        if (!$payment_ref) {
            echo "<h2>Error</h2><p>Missing payment reference.</p>";
            return;
        }
    
        $api_username = constant('EVERYPAY_API_USERNAME');
        $auth_key = constant('EVERYPAY_AUTH_KEY');
        $api_url = constant('EVERYPAY_URL');
    
        // ✅ FIX: Add api_username and detailed to the URL
        $url = "{$api_url}{$payment_ref}?api_username={$api_username}&detailed=true";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . base64_encode($api_username . ':' . $auth_key),
                'Content-Type: application/json'
            ]
        ]);
    
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            echo "<h2>cURL Error:</h2><p>$err</p>";
            return;
        }
    
        $result = json_decode($response, true);
    
        if (
            !$result ||
            !isset($result['payment_state'], $result['payment_reference'], $result['order_reference'])
        ) {
            echo "<h2>Error</h2><p>Invalid response from EveryPay.</p>";
            return;
        }
    
        $order_reference = $result['order_reference'];
        $payment_state = $result['payment_state'];
    
        if ($payment_state === 'settled') {

            $order_id = (int) str_replace('ORDER-25-', '', $order_reference); // gives id

            $registration = $this->model->get_where($order_id, 'lesson_registrations');
            if ($registration === false) {
                echo "<h2>Error</h2><p>Registration not found.</p>";
                return;
            }

            // Idempotency: EveryPay may call this URL more than once and the customer
            // can refresh the page. Only settle (and count the place) the first time.
            if ($registration->status === 'settled') {
                redirect('lessons/thankyou');
            }

            // Defence in depth: when EveryPay reports the paid amount, make sure it
            // matches the lesson price before confirming the place.
            $sql = "SELECT l.price
                    FROM lesson_schedules ls
                    JOIN lessons l ON ls.lesson_id = l.id
                    WHERE ls.id = :schedule_id";
            $price_rows = $this->model->query_bind($sql, ['schedule_id' => $registration->schedule_id], 'array');
            if (isset($result['amount'], $price_rows[0])) {
                $paid     = number_format((float) $result['amount'], 2, '.', '');
                $expected = number_format((float) $price_rows[0]['price'], 2, '.', '');
                if ($paid !== $expected) {
                    echo "<h2>Error</h2><p>Payment amount mismatch.</p>";
                    return;
                }
            }

            // Mark the registration settled
            $data['status'] = 'settled';
            $data['payment_reference'] = $payment_ref;
            $this->model->update($order_id, $data, 'lesson_registrations');

            // Increment reserved_places in lesson_schedules
            $sql = "UPDATE lesson_schedules SET reserved_places = reserved_places + 1 WHERE id = ?";
            $this->model->query_bind($sql, [$registration->schedule_id]);

            // Send confirmation email
            $this->_curl_mail($order_id);

            redirect('lessons/thankyou');

        } else {

            echo "<h2>Payment Failed</h2><p>Status: $payment_state</p>";

        }
    }

    function _curl_mail($order_id): void {
        $data['order_id'] = $order_id;
        $sql = "SELECT lu.name, lu.email, ls.date, ls.start_time, l.name AS lesson_name
            FROM lesson_registrations lr
            JOIN lesson_users lu ON lr.user_id = lu.id
            JOIN lesson_schedules ls ON lr.schedule_id = ls.id
            JOIN lessons l ON ls.lesson_id = l.id
            WHERE lr.id = :order_id";
        $lesson_data = $this->model->query_bind($sql, $data, 'array');

        $curl = curl_init();

        $name = $lesson_data[0]['name'] ?? 'Nenurodytas Vartotojas';
        $pamokos_data = $lesson_data[0]['date'] ?? 'Nenurodyta Pamokos Data';
        $pamokos_laikas = $lesson_data[0]['start_time'] ?? 'Nenurodytas Pamokos Laikas';
        $email = $lesson_data[0]['email'] ?? 'Nenurodytas Vartotojo El. Paštas';
        $lesson_name = $lesson_data[0]['lesson_name'] ?? 'Nenurodyta Pamoka';

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
            "subject" => "Surf Pamokos Registracija",
            "templateId" => 35,
            "params" => [
                "name" => $name,
                "pamokos_data" => $pamokos_data,
                "pamokos_laikas" => $pamokos_laikas,
                "lesson_name" => $lesson_name
            ],
            "headers" => [
                "X-Mailin-custom" => "custom_header_1:custom_value_1|custom_header_2:custom_value_2|custom_header_3:custom_value_3|custom_header_4:custom_value_4",
                "charset" => "iso-8859-1"
            ]
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.brevo.com/v3/smtp/email",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "api-key: " . constant('BREVO_API'),
                "content-type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
        ]);

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        //if ($http_code === 201) {
        //   echo "Email sent successfully!";
        // } else {
        //   echo "Error sending email: " . $response;
        // }

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
            $data['headline'] = 'Update Lesson Registration Record';
            $data['cancel_url'] = BASE_URL.'lessons-registrations/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Lesson Registration Record';
            $data['cancel_url'] = BASE_URL.'lessons-registrations/manage';
        }

        $data['form_location'] = BASE_URL.'lessons-registrations/submit/'.$update_id;
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
            $sql = 'select * from lesson_registrations ORDER BY id';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Lesson Registrations';
            $all_rows = $this->model->get('id', 'lesson_registrations');
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->get_limit();
        $pagination_data['pagination_root'] = 'lessons-registrations/manage';
        $pagination_data['record_name_plural'] = 'lesson_registrations';
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
            redirect('lessons-registrations/manage');
        }

        $data = $this->get_data_from_db($update_id);
        $data['token'] = $token;

        if ($data === false) {
            redirect('lessons-registrations/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['headline'] = 'Lesson Registration Information';
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

            $this->validation->set_rules('user_id', 'user_id', 'required|max_length[11]|numeric|greater_than[0]|integer');
            $this->validation->set_rules('schedule_id', 'schedule_id', 'required|max_length[11]|numeric|greater_than[0]|integer');
            $this->validation->set_rules('registration_date', 'registration_date', 'required');

            $result = $this->validation->run();

            if ($result === true) {

                $update_id = (int) segment(3);
                $data = $this->get_data_from_post();
                $data['registration_date'] = str_replace(' at ', '', $data['registration_date']);
                $data['registration_date'] = date('Y-m-d H:i', strtotime($data['registration_date']));
                
                if ($update_id>0) {
                    //update an existing record
                    $this->model->update($update_id, $data, 'lesson_registrations');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    //insert the new record
                    $update_id = $this->model->insert($data, 'lesson_registrations');
                    $flash_msg = 'The record was successfully created';
                }

                set_flashdata($flash_msg);
                redirect('lessons-registrations/show/'.$update_id);

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
            $params['module'] = 'lesson_registrations';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'lesson_registrations');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('lessons-registrations/manage');
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
        redirect('lessons-registrations/manage');
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
        $record_obj = $this->model->get_where($update_id, 'lesson_registrations');

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
        $data['user_id'] = post('user_id', true);
        $data['schedule_id'] = post('schedule_id', true);
        $data['registration_date'] = post('registration_date', true);        
        return $data;
    }

}