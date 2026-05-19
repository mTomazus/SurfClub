<?php
class Products extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100); 

    //-----------------------------------------------------------
    //------------------- PRODUCT PAGES -------------------------
    //-----------------------------------------------------------

    public function index(): void {
        $this->_listing('best', 'Perkamiausios prekės');
    }

    public function naujos(): void {
        $this->_listing('new', 'Naujausios prekės');
    }

    public function surf(): void {
        $this->_listing('surf', 'Surf prekės');
    }

    public function beach(): void {
        $this->_listing('beach', 'Pliažo prekės');
    }

    private function _listing(string $slug, string $page_title): void {
        $category = $this->model->get_one_where('slug', $slug, 'products_categories');
        if (!$category) {
            echo "Category not found.";
            return;
        }
        $sql = "SELECT p.*
                FROM products p
                JOIN products_items_categories pic ON p.id = pic.product_id
                WHERE pic.category_id = ?
                AND p.status = 'active'
                ORDER BY p.id DESC";
        $products = $this->model->query_bind($sql, [$category->id], 'object');
        $data['products'] = $this->_add_picture_paths($products);
        $data['page_title'] = $page_title;
        $data['view_module'] = 'products';
        $data['view_file'] = 'listing';
        $this->template('shop_area', $data);
    }

    public function item() {   // show item
        $product_id = (int)segment(3);

        $sql = "SELECT p.id, p.name, p.description, p.short_desc, p.price, p.image,
                v.option_name, v.option_value, v.stock
                FROM products p
                LEFT JOIN products_variants v ON p.id = v.product_id
                WHERE p.id = ?
                AND p.status = 'active'
                ORDER BY p.id DESC";
        $products = $this->model->query_bind($sql, [$product_id], 'object');
       
        if (!$products) {
            // Handle the case where the product is not found
            die('Product not found.');
        }
    
        $data['products'] = $this->_add_picture_paths($products);
        $data['view_file'] = 'item_show';

        $this->template('shop_area', $data);
    }

    //-----------------------------------------------------------
    //------------------ PRODUCT PAGES END ----------------------
    //-----------------------------------------------------------

    function _add_picture_paths($products) {

        // Handle single product object
        if (is_object($products)) {
            $products->picture_path = BASE_URL . 'products_module/images/products_pics/' . $products->id . '/' . $products->image;
            return $products;
        }

        // Handle array of products
        foreach($products as $key => $value) {
            $picture_path = BASE_URL.'products_module/images/products_pics/'.$value->id.'/'.$value->image;
            $products[$key]->picture_path = $picture_path;
        }

        return $products;
    }

    function _get_omniva_lockers() {

        $cache_file = APPPATH . 'modules/products/assets/omniva_cache.json';

        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < 86400) {
            $jsonData = file_get_contents($cache_file);
        } else {
            $jsonData = file_get_contents("https://www.omniva.ee/locations.json");
            if ($jsonData === false) {
                // Fall back to stale cache if available
                if (file_exists($cache_file)) {
                    $jsonData = file_get_contents($cache_file);
                } else {
                    die("Failed to load location data.");
                }
            } else {
                file_put_contents($cache_file, $jsonData);
            }
        }

        return json_decode($jsonData, true);
    }

    function checkout(): void {
        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            redirect('products'); // Or show empty cart view
        }
    
        $product_ids = array_keys($cart);
        $products = $this->model->get_where_in('id', $product_ids, 'products');
    
        $data['cart'] = $cart;
        $data['products'] = $products;

        $locations = $this->_get_omniva_lockers();

        $data['locations'] = $locations;

        $data['form_location'] = 'products/process_order';
        $data['view_file'] = 'checkout';
        $this->template('shop_area', $data);
    }

    //-----------------------------------------------------------
    //------------------   CART CONTROLS   ----------------------
    //-----------------------------------------------------------

    function add_to_cart() {

        $productId = (int) post('product_id');
        $quantity  = max(1, (int) ($_POST['quantity'] ?? 1));

        $product = $this->model->get_where($productId, 'products');
        $stock   = $product ? (int) $product->in_stock : 0;

        if ($stock === 0) {
            set_flashdata('Atsiprašome, šios prekės nėra sandėlyje.');
            redirect('products/item/' . $productId);
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $current  = $_SESSION['cart'][$productId] ?? 0;
        $new_qty  = min($current + $quantity, $stock);
        $_SESSION['cart'][$productId] = $new_qty;

        redirect('products');

    }

    function update_cart() {

        $product_id = post('product_id');
        $action     = post('action');

        if ($action === 'increase') {
            $product = $this->model->get_where((int) $product_id, 'products');
            $stock   = $product ? (int) $product->in_stock : 0;
            $current = $_SESSION['cart'][$product_id] ?? 0;
            if ($current < $stock) {
                $_SESSION['cart'][$product_id] = $current + 1;
            }
        } elseif ($action === 'decrease') {
            if (isset($_SESSION['cart'][$product_id])) {
                if ($_SESSION['cart'][$product_id] > 1) {
                    $_SESSION['cart'][$product_id]--;
                } else {
                    unset($_SESSION['cart'][$product_id]);
                }
            }
        }

        echo '';
    }

    function remove_from_cart() {

        $id = segment(3, 'int') ?? null;
        if ($id && isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }

        redirect('products/cart');

    }

    public function cart_panel(): void {
        $cart = $_SESSION['cart'] ?? [];
        $products = [];
        if (!empty($cart)) {
            $product_ids = array_keys($cart);
            $products = $this->model->get_where_in('id', $product_ids, 'products');
            $products = $this->_add_picture_paths($products);
        }
        extract(['cart' => $cart, 'products' => $products]);
        include APPPATH . 'modules/products/views/cart_panel.php';
    }

    public function remove_from_cart_ajax(): void {
        $id = segment(3, 'int') ?? null;
        if ($id && isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        echo '';
    }

    public function cart() {
        
        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            $data['view_file'] = 'cart';
            $this->template('shop_area', $data);
            return;
        }

        $product_ids = array_keys($cart);

        // Fetch products
        $products = [];
        if (!empty($product_ids)) {
            $products = $this->model->get_where_in('id', $product_ids, 'products');
            $products = $this->_add_picture_paths($products);
        }

        $data['cart'] = $cart;
        $data['products'] = $products;
        $data['view_module'] = 'products';
        $data['view_file'] = 'cart';
        $this->template('shop_area', $data);
    }

    //-----------------------------------------------------------
    //-----------------   PAYMENT PROCESS   ---------------------
    //-----------------------------------------------------------

    function process_order() {

        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            set_flashdata('error', 'Your cart is empty.');
            redirect('products/cart');
        }
        
        $this->validation->set_rules('customer_name', 'customer_name', 'required|min_length[4]|max_length[255]');
        $this->validation->set_rules('delivery', 'delivery', 'required');
        $this->validation->set_rules('address', 'address', 'min_length[5]|max_length[255]');
        $this->validation->set_rules('phone', 'phone', 'required|min_length[8]|max_length[15]');
        $this->validation->set_rules('email', 'email', 'required|valid_email');
        $this->validation->set_rules('sutikimas', 'sutikimas', 'required');

        $result = $this->validation->run();

        if ($result === true) {

            $data['customer_name'] = post('customer_name', true);
            $data['phone'] = post('phone', true);
            $data['email'] = post('email', true);
            $data['delivery'] = post('delivery', true);
            if ($data['delivery'] === 'omniva') {
                $data['address'] = post('address', true);
            } else {
                $data['address'] = '';
            }   

            // Insert order
            $order_id = $this->model->insert($data, 'products_orders');

            // Get product prices
            $product_ids = array_keys($cart);

            $products = $this->model->get_where_in('id', $product_ids, 'products');

            $total_amount = 0;
            foreach ($products as $product) {
                $qty = $cart[$product->id];
                $total_amount += $product->price * $qty;
                $order_data = [
                    'order_id' => $order_id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $product->price
                ];
                $this->model->insert($order_data, 'products_orders_items');
            }

            // Generate payment link using EveryPay
            $payment_link = $this->_get_everypay_link($order_id, $total_amount);

            if ($payment_link) {
                unset($_SESSION['cart']);

                // Store payment link in session or pass via redirect with order_id
                $_SESSION['payment_link'] = $payment_link;
                // redirect('products/payment/' . $order_id);
                redirect($payment_link);

            } else {
                echo "Payment initialization failed.";
            }

        }

        $this->checkout();

    }

    function payment() {
        $order_id = (int) segment(3);
        // Fetch order + items
        $data['order'] = $this->model->get_one_where('id', $order_id, 'products_orders');

        $sql = "
            SELECT p.name, p.image, i.quantity, i.price
            FROM products_orders_items i
            JOIN products p ON i.product_id = p.id
            WHERE i.order_id = ?";
        
        $data['items'] = $this->model->query_bind($sql, [$order_id], 'object');

        // Get the EveryPay iframe URL
        $data['payment_link'] = $_SESSION['payment_link'] ?? null;
    
        $data['view_file'] = 'payment_page';
        $this->template('shop_area', $data);
    }

    function payment_result() {
        $payment_ref = $_GET['payment_reference'] ?? null;
        if (!$payment_ref) {
            echo "<h2>Error</h2><p>Missing payment reference.</p>";
            return;
        }

        $result = $this->_verify_everypay_payment($payment_ref);
        if (!$result) {
            echo "<h2>Error</h2><p>Could not verify payment with EveryPay.</p>";
            return;
        }

        $payment_state   = $result['payment_state'];
        $order_reference = $result['order_reference'];

        if ($payment_state === 'settled') {
            $order_id = (int) str_replace('ORDER-', '', $order_reference);
            $this->_confirm_order($order_id, $payment_ref);
            redirect('products/thank_you');
        } else {
            echo "<h2>Payment Failed</h2><p>Status: $payment_state</p>";
        }
    }

    private function _verify_everypay_payment(string $payment_ref): ?array {
        $api_username = constant('EVERYPAY_API_USERNAME');
        $auth_key     = constant('EVERYPAY_AUTH_KEY');
        $api_url      = constant('EVERYPAY_URL');

        $url = "{$api_url}{$payment_ref}?api_username={$api_username}&detailed=true";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Basic ' . base64_encode($api_username . ':' . $auth_key),
                'Content-Type: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($err || !$response) {
            return null;
        }

        $result = json_decode($response, true);
        if (!$result || !isset($result['payment_state'], $result['order_reference'])) {
            return null;
        }

        return $result;
    }

    private function _confirm_order(int $order_id, string $payment_ref): void {
        // Idempotency: skip if this order was already confirmed (e.g. duplicate webhook)
        $rows = $this->model->query_bind(
            "SELECT status FROM products_orders WHERE id = ? LIMIT 1",
            [$order_id],
            'object'
        );
        if (!$rows || $rows[0]->status === 'paid') {
            return;
        }

        $this->model->update($order_id, [
            'status'            => 'paid',
            'payment_reference' => $payment_ref,
        ], 'products_orders');

        $items = $this->model->query_bind(
            "SELECT product_id, quantity FROM products_orders_items WHERE order_id = ?",
            [$order_id],
            'object'
        );

        foreach ($items as $item) {
            $this->model->query_bind(
                "UPDATE products SET in_stock = GREATEST(0, in_stock - ?) WHERE id = ?",
                [$item->quantity, $item->product_id]
            );
        }
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
            "order_reference" => "ORDER-$order_id",
            "nonce" => uniqid(),
            "timestamp" => date('c'),
            "customer_url" => BASE_URL . "products/payment_result"
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

    function thank_you() {
        $data['view_file'] = 'thankyou';
        $this->template('shop_area', $data);
    }

    function webhook() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $raw_input = file_get_contents('php://input');
        $payload   = json_decode($raw_input, true);

        if (!isset($payload['payment_reference'], $payload['order_reference'])) {
            http_response_code(400);
            die('Invalid webhook payload');
        }

        $payment_ref = $payload['payment_reference'];
        $order_ref   = $payload['order_reference'];
        $order_id    = (int) str_replace('ORDER-', '', $order_ref);

        // Verify payment status directly with EveryPay API — do not trust raw payload
        $result = $this->_verify_everypay_payment($payment_ref);
        if (!$result) {
            http_response_code(502);
            die('Could not verify payment with EveryPay');
        }

        $payment_state = $result['payment_state'];

        if ($payment_state === 'settled') {
            $this->_confirm_order($order_id, $payment_ref);
        } else {
            $this->model->update($order_id, [
                'status'            => $payment_state,
                'payment_reference' => $payment_ref,
            ], 'products_orders');
        }

        http_response_code(200);
        echo 'Webhook received';
    }

    //--------------------------------------------------------------------
    //---------------------- ADMIN PANEL START ---------------------------
    //--------------------------------------------------------------------
    
    // Display a webpage with a form for creating or updating a record.

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

        $cats = $this->model->get('id', 'products_categories');
        $category_options = [];
        foreach ($cats as $cat) {
            $category_options[$cat->id] = $cat->name;
        }
        $data['category_options'] = $category_options;

        if ($update_id > 0) {
            $rows = $this->model->query_bind(
                "SELECT category_id FROM products_items_categories WHERE product_id = ?",
                [$update_id], 'object'
            );
            $selected = [];
            foreach ($rows as $row) {
                $selected[] = $row->category_id;
            }
            $data['selected_categories'] = $selected;
            $data['headline'] = 'Update Product Record';
            $data['cancel_url'] = BASE_URL.'products/show/'.$update_id;
        } else {
            $data['selected_categories'] = [];
            $data['headline'] = 'Create New Product Record';
            $data['cancel_url'] = BASE_URL.'products/manage';
        }

        $data['form_location'] = BASE_URL.'products/submit/'.$update_id;
        $data['view_file'] = 'create';
        $this->template('admin', $data);
    }

    // Display a webpage to manage records.

    public function manage(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if (segment(4) !== '') {
            $data['headline'] = 'Search Results';
            $searchphrase = trim($_GET['searchphrase']);
            $params['name'] = '%'.$searchphrase.'%';
            $params['status'] = '%'.$searchphrase.'%';
            $sql = 'select * from products
            WHERE name LIKE :name
            OR status LIKE :status
            ORDER BY id';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Products';
            $all_rows = $this->model->get('id');
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->get_limit();
        $pagination_data['pagination_root'] = 'products/manage';
        $pagination_data['record_name_plural'] = 'products';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->reduce_rows($all_rows);
        $data['selected_per_page'] = $this->get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'products';
        $data['view_file'] = 'manage';
        $this->template('admin', $data);
    }

    // Display a webpage showing information for an individual record.

    public function show(): void {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();
        $update_id = (int) segment(3);

        if ($update_id === 0) {
            redirect('products/manage');
        }

        $data = $this->get_data_from_db($update_id);
        $data['token'] = $token;

        if ($data === false) {
            redirect('products/manage');
        } else {

             //generate picture folders, if required
             $picture_settings = $this->_init_picture_settings();
             $this->_make_sure_got_destination_folders($update_id, $picture_settings);
 
             //attempt to get the current picture
             $column_name = $picture_settings['target_column_name'];
 
             if ($data[$column_name] !== '') {
                 //we have a picture - display picture preview
                 $data['draw_picture_uploader'] = false;
             } else {
                 //no picture - draw upload form
                 $data['draw_picture_uploader'] = true;
             }

            $data['update_id'] = $update_id;
            $data['headline'] = 'Product Information';
            $data['view_file'] = 'show';
            $this->template('admin', $data);
        }
    }

    // ------------------------------------------------------------------
    // ---------------- Handle submitted record data. -------------------
    // ------------------------------------------------------------------
    public function submit(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();
    
        if (post('submit', true) !== 'Submit') {
            redirect('products/manage');
        }
    
        $this->run_validation();
    
        if ($this->validation->run() === true) {
            $update_id = (int) segment(3);
            $data = $this->get_data_from_post();
    
            if ($update_id > 0) {
                $this->update_product($update_id, $data);
                $flash_msg = 'The record was successfully updated';
            } else {
                $update_id = $this->create_product($data);
                $flash_msg = 'The record was successfully created';
            }
    
            $this->save_product_categories($update_id);
            $this->save_product_variants($update_id);
    
            set_flashdata($flash_msg);
            redirect('products/show/' . $update_id);
    
        } else {
            $this->create(); // fallback to form
        }
    }
    
    private function run_validation(): void {
        $this->validation->set_rules('name', 'name', 'required|min_length[2]|max_length[255]');
        $this->validation->set_rules('description', 'description', 'required|min_length[2]');
        $this->validation->set_rules('short_desc', 'short_desc', 'max_length[255]');
        $this->validation->set_rules('price', 'price', 'required|greater_than[0]|numeric');
        $this->validation->set_rules('discount_price', 'discount_price', 'greater_than[0]|numeric');
        $this->validation->set_rules('in_stock', 'in_stock', 'required|integer');
        $this->validation->set_rules('status', 'status', 'required|min_length[2]|max_length[255]');
    }

    private function save_product_categories(int $product_id): void {
        $categories = post('categories');
        if (!empty($categories) && is_array($categories)) {
            $existing = $this->model->query_bind(
                "SELECT category_id FROM products_items_categories WHERE product_id = ?",
                [$product_id], 'object'
            );
            $existing_ids = [];
            foreach ($existing as $row) {
                $existing_ids[(int) $row->category_id] = true;
            }
            foreach ($categories as $category_id) {
                if (!isset($existing_ids[(int) $category_id])) {
                    $this->model->insert([
                        'product_id'  => $product_id,
                        'category_id' => $category_id
                    ], 'products_items_categories');
                }
            }
        }
    }

    private function save_product_variants(int $product_id): void {
        $variants = post('variants');
        if (!empty($variants) && is_array($variants)) {
            $existing = $this->model->query_bind(
                "SELECT option_name, option_value FROM products_variants WHERE product_id = ?",
                [$product_id], 'object'
            );
            $existing_keys = [];
            foreach ($existing as $row) {
                $existing_keys[$row->option_name . ':' . $row->option_value] = true;
            }
            foreach ($variants as $variant) {
                $variant = trim($variant);
                if ($variant !== '' && strpos($variant, ':') !== false) {
                    list($option, $value) = explode(':', $variant, 2);
                    $key = trim($option) . ':' . trim($value);
                    if (!isset($existing_keys[$key])) {
                        $this->model->insert([
                            'product_id'   => $product_id,
                            'option_name'  => trim($option),
                            'option_value' => trim($value),
                            'is_active'    => 1
                        ], 'products_variants');
                    }
                }
            }
        }
    }

    private function update_product(int $id, array $data): void {
        $this->model->update($id, $data, 'products');
    
        // Clean up old links
        $this->model->query_bind("DELETE FROM products_variants WHERE product_id = ?", [$id]);
        $this->model->query_bind("DELETE FROM products_items_categories WHERE product_id = ?", [$id]);
    }
    
    private function create_product(array $data): int {
        return $this->model->insert($data, 'products');
    }
    // ------------------------------------------------------------------
    // ----------------------- SUBMIT END -------------------------------
    // ------------------------------------------------------------------

    // Handle submitted request for deletion.
    
    public function submit_delete(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit');
        $params['update_id'] = (int) segment(3);

        if (($submit === 'Yes - Delete Now') && ($params['update_id']>0)) {
            //delete all of the comments associated with this record
            $sql = 'delete from trongate_comments where target_table = :module and update_id = :update_id';
            $params['module'] = 'products';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'products');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('products/manage');
        }
    }

    /**
     * Set the number of items per page.
     */
    public function set_per_page(int $selected_index): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if (!is_numeric($selected_index)) {
            $selected_index = $this->per_page_options[1];
        }

        $_SESSION['selected_per_page'] = $selected_index;
        redirect('products/manage');
    }

    /**
     * Get the selected number of items per page.
     */
    private function get_selected_per_page(): int {
        $selected_per_page = (isset($_SESSION['selected_per_page'])) ? $_SESSION['selected_per_page'] : 1;
        return $selected_per_page;
    }

    /**
     * Reduce fetched table rows based on offset and limit.
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
     */
    private function get_data_from_db(int $update_id): ?array {
        $record_obj = $this->model->get_where($update_id, 'products');

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
     */
    private function get_data_from_post(): array {
        $data['name'] = post('name', true);
        $data['description'] = post('description', true);
        $data['short_desc'] = post('short_desc', true);
        $data['price'] = post('price', true);
        $data['discount_price'] = post('discount_price', true);
        $data['in_stock'] = post('in_stock', true);
        $data['status'] = post('status', true);
        $data['image'] = post('image', true);       
        return $data;
    }

    function _init_picture_settings() { 
        $picture_settings['max_file_size'] = 2000;
        $picture_settings['max_width'] = 1500;
        $picture_settings['max_height'] = 2250;
        $picture_settings['resized_max_width'] = 500;
        $picture_settings['resized_max_height'] = 750;
        $picture_settings['destination'] = 'products_pics';
        $picture_settings['target_column_name'] = 'image';
        return $picture_settings;
    }

    function _make_sure_got_destination_folders($update_id, $picture_settings) {
        $destination = $picture_settings['destination'];
        $destination = 'modules/'.segment(1).'/assets/images/'.$destination;
        $target_dir = APPPATH.$destination.'/'.$update_id;

        if (!file_exists($target_dir)) {
            //generate the image folder
            mkdir($target_dir, 0777, true);
        }

    }

    function submit_upload_picture($update_id) {

        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if ($_FILES['picture']['name'] == '') {
            redirect($_SERVER['HTTP_REFERER']);
        }

        $submit = post('submit');

        if ($submit == 'Upload') {
            $picture_settings = $this->_init_picture_settings();
            extract($picture_settings);

            $validation_str = 'allowed_types[gif,jpg,jpeg,webp,png]|max_size['.$max_file_size.']|max_width['.$max_width.']|max_height['.$max_height.']';
            $this->validation->set_rules('image', 'product image', $validation_str);

            $result = $this->validation->run();

            if ($result == true) {

                $config['destination'] = $destination.'/'.$update_id;
                $config['max_width'] = $resized_max_width;
                $config['max_height'] = $resized_max_height;

                //upload the image
                $this->upload_picture_alt($config);
                
                //update the database
                $data[$target_column_name] = $_FILES['picture']['name'];
                $this->model->update($update_id, $data);

                $flash_msg = 'The image was successfully uploaded';
                set_flashdata($flash_msg);
                redirect($_SERVER['HTTP_REFERER']);

            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

    }

    function upload_picture_alt($data) {
        //check for valid image width and mime type
        $userfile = array_keys($_FILES)[0];
        $target_file = $_FILES[$userfile];

        $dimension_data = getimagesize($target_file['tmp_name']);
        $image_width = $dimension_data[0];

        if (!is_numeric($image_width)) {
            die('ERROR: non numeric image width');
        }

        $content_type = mime_content_type($target_file['tmp_name']);

        $str = substr($content_type, 0, 6);

        if ($str !== 'image/') {
            die('ERROR: not an image.');
        }

        $tmp_name = $target_file['tmp_name'];

        $data['image'] = new Image($tmp_name);

        $dir_path = 'modules/'.segment(1).'/assets/images/';
        $data['destination'] = $dir_path.$data['destination'];
        $data['filename'] = '../'.$data['destination'].'/'.$target_file['name'];
        $data['tmp_file_width'] = $data['image']->getWidth();
        $data['tmp_file_height'] = $data['image']->getHeight();

        if (!isset($data['max_width'])) {
            $data['max_width'] = NULL;
        }

        if (!isset($data['max_height'])) {
            $data['max_height'] = NULL;
        }

        $this->save_that_pic_alt($data);
       
    }

    function save_that_pic_alt($data) {
        extract($data);
        $reduce_width = false;
        $reduce_height = false;

        if (!isset($data['compression'])) {
            $compression = 100;
        } else {
            $compression = $data['compression'];
        }

        if (!isset($data['permissions'])) {
            $permissions = 775;
        } else {
            $permissions = $data['permissions'];
        }

        //do we need to resize the picture?
        if ((isset($max_width)) && ($tmp_file_width>$max_width)) {
            $reduce_width = true;
        }

        if ((isset($max_height)) && ($tmp_file_width>$max_height)) {
            $reduce_height = true;
        }

        //resize rules figured out, let's rock...
        if (($reduce_width == true) && ($reduce_height == false)) {
            $image->resizeToWidth($max_width);
            $image->save($filename, $compression);
        }

        if (($reduce_width == false) && ($reduce_height == true)) {
            $image->resizeToHeight($max_height);
            $image->save($filename, $compression);
        }

        if (($reduce_width == false) && ($reduce_height == false)) {
            $image->save($filename, $compression);
        }

        if (($reduce_width == true) && ($reduce_height == true)) {
            $image->resizeToWidth($max_width);
            $image->resizeToHeight($max_height);
            $image->save($filename, $compression);
        }
    }

    function ditch_picture($update_id) {

        if (!is_numeric($update_id)) {
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $result = $this->model->get_where($update_id);

        if ($result == false) {
            redirect($_SERVER['HTTP_REFERER']);
        }

        $target_dir = APPPATH.'modules/products/assets/images/products_pics/'.$update_id;
        
        $this->_rrmdir($target_dir);

        $picture_settings = $this->_init_picture_settings();
        $target_column_name = $picture_settings['target_column_name'];
        $data[$target_column_name] = '';
        $this->model->update($update_id, $data);
        
        $flash_msg = 'The image was successfully deleted';
        set_flashdata($flash_msg);
        redirect($_SERVER['HTTP_REFERER']);
    }

    function _limit_text($text, $limit) {
        
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos   = array_keys($words);
            $text  = substr($text, 0, $pos[$limit]) . '...';
        }

        return $text;
    }

    function _rrmdir($dir) { 
        if (is_dir($dir)) { 
            $objects = scandir($dir);

            foreach ($objects as $object) { 
                if ($object != "." && $object != "..") { 
                    if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
                        $this->_rrmdir($dir. DIRECTORY_SEPARATOR .$object);
                    else
                    unlink($dir. DIRECTORY_SEPARATOR .$object); 
                    } 
                }
            rmdir($dir); 
        } 
    }

    //----------- add to wishlist --------------
    function add_to_wishlist() {
        $product_id = (int) segment(3);
        
        // Get current cookie wishlist
        $wishlist = isset($_COOKIE['wishlist']) ? json_decode($_COOKIE['wishlist'], true) : [];
    
        if (!in_array($product_id, $wishlist)) {
            $wishlist[] = $product_id;
        }
    
        // Save back to cookie
        setcookie('wishlist', json_encode($wishlist), time() + 60*60*24*30, '/');
        
        redirect('products/item/' . $product_id);
    }

    function remove_from_wishlist() {
        $product_id = (int) segment(3);
    
        $wishlist = isset($_COOKIE['wishlist']) ? json_decode($_COOKIE['wishlist'], true) : [];
    
        $wishlist = array_diff($wishlist, [$product_id]);
    
        setcookie('wishlist', json_encode(array_values($wishlist)), time() + 60*60*24*30, '/');
    
        redirect('products/wishlist');
    }

    //-----------  wishllist page --------------
    function wishlist() {
        $ids = isset($_COOKIE['wishlist']) ? json_decode($_COOKIE['wishlist'], true) : [];
    
        if (empty($ids)) {
            $data['products'] = [];
        } else {
            $data['products'] = $this->model->get_where_in('id', $ids, 'products');
        }
    
        $data['view_module'] = 'products';
        $data['view_file'] = 'wishlist';
        $this->template('shop_area', $data);
    }

}