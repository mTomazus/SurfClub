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

    public function category(): void {
        $slug = segment(3);
        if (!$slug) {
            $this->template('error_404');
            return;
        }
        $category = $this->model->get_one_where('slug', $slug, 'products_categories');
        if (!$category) {
            $this->template('error_404');
            return;
        }
        $this->_listing($slug, $category->name);
    }

    private function _listing(string $slug, string $page_title): void {
        $category = $this->model->get_one_where('slug', $slug, 'products_categories');
        if (!$category) {
            $this->template('error_404');
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

        $sql = "SELECT p.id, p.name, p.description, p.short_desc, p.price, p.discount_price, p.image,
                v.id AS variant_id, v.option_name, v.option_value, v.stock
                FROM products p
                LEFT JOIN products_variants v ON p.id = v.product_id AND v.is_active = 1
                WHERE p.id = ?
                AND p.status = 'active'
                ORDER BY v.id ASC";
        $products = $this->model->query_bind($sql, [$product_id], 'object');
       
        if (!$products) {
            $this->template('error_404');
            return;
        }
    
        $data['products'] = $this->_add_picture_paths($products);
        $data['gallery'] = $this->_get_gallery_images($product_id);
        $data['view_file'] = 'item_show';

        $this->template('shop_area', $data);
    }

    /**
     * Build public URLs for a product's additional gallery pictures
     * (uploaded via trongate_filezone). The single `image` column remains
     * the cover/main picture; these are the extra shots shown as thumbnails.
     */
    private function _get_gallery_images(int $product_id): array {
        $settings = $this->_init_filezone_settings();
        $dir = APPPATH . 'modules/products/assets/' . $settings['destination'] . '/' . $product_id;

        $urls = [];
        if (is_dir($dir)) {
            foreach (scandir($dir) as $file) {
                if ($file === '.' || $file === '..' || $file === '.DS_Store' || $file === 'thumbnails') {
                    continue;
                }
                $urls[] = BASE_URL . 'products_module/' . $settings['destination'] . '/' . $product_id . '/' . $file;
            }
        }

        return $urls;
    }

    //-----------------------------------------------------------
    //------------------ PRODUCT PAGES END ----------------------
    //-----------------------------------------------------------

    function _add_picture_paths($products) {

        // Handle single product object
        if (is_object($products)) {
            $products->picture_path = $this->_cover_path($products->id, $products->image ?? '');
            return $products;
        }

        // Handle array of products
        foreach($products as $key => $value) {
            $products[$key]->picture_path = $this->_cover_path($value->id, $value->image ?? '');
        }

        return $products;
    }

    /**
     * Build a product's cover image URL, falling back to a placeholder when the
     * product has no cover so the storefront never renders a broken <img>.
     */
    private function _cover_path($product_id, string $image): string {
        if ($image === '') {
            return BASE_URL . 'products_module/images/placeholder.svg';
        }
        return BASE_URL . 'products_module/images/products_pics/' . $product_id . '/' . rawurlencode($image);
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
                    return [];
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

        $productId  = (int) post('product_id');
        $quantity   = max(1, (int) ($_POST['quantity'] ?? 1));
        $variant_id = (int) post('variant_id');

        if ($variant_id > 0) {
            $variant = $this->model->get_one_where('id', $variant_id, 'products_variants');
            $stock   = $variant ? (int) $variant->stock : 0;
        } else {
            $has = $this->model->query_bind(
                "SELECT COUNT(*) AS cnt FROM products_variants WHERE product_id = ? AND is_active = 1",
                [$productId], 'object'
            );
            if ($has && (int) $has[0]->cnt > 0) {
                set_flashdata('Prašome pasirinkti variantą prieš pridedant į krepšelį.');
                redirect('products/item/' . $productId);
            }
            $product = $this->model->get_where($productId, 'products');
            $stock   = $product ? (int) $product->in_stock : 0;
        }

        if ($stock === 0) {
            set_flashdata('Atsiprašome, šios prekės nėra sandėlyje.');
            redirect('products/item/' . $productId);
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $entry   = $_SESSION['cart'][$productId] ?? ['qty' => 0, 'variant_id' => null];
        $current = is_array($entry) ? (int) $entry['qty'] : (int) $entry;
        $new_qty = min($current + $quantity, $stock);

        $_SESSION['cart'][$productId] = [
            'qty'        => $new_qty,
            'variant_id' => $variant_id > 0 ? $variant_id : null,
        ];

        redirect('products');

    }

    function update_cart() {

        $product_id = post('product_id');
        $action     = post('action');

        $entry      = $_SESSION['cart'][$product_id] ?? ['qty' => 0, 'variant_id' => null];
        $current    = is_array($entry) ? (int) $entry['qty'] : (int) $entry;
        $variant_id = is_array($entry) ? ($entry['variant_id'] ?? null) : null;

        if ($action === 'increase') {
            if ($variant_id) {
                $variant = $this->model->get_one_where('id', $variant_id, 'products_variants');
                $stock   = $variant ? (int) $variant->stock : 0;
            } else {
                $product = $this->model->get_where((int) $product_id, 'products');
                $stock   = $product ? (int) $product->in_stock : 0;
            }
            if ($current < $stock) {
                $_SESSION['cart'][$product_id] = ['qty' => $current + 1, 'variant_id' => $variant_id];
            }
        } elseif ($action === 'decrease') {
            if (isset($_SESSION['cart'][$product_id])) {
                if ($current > 1) {
                    $_SESSION['cart'][$product_id] = ['qty' => $current - 1, 'variant_id' => $variant_id];
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
            set_flashdata('Jūsų krepšelis tuščias.');
            redirect('products/cart');
        }

        // Housekeeping: release checkouts that were started but never paid.
        $this->_expire_stale_pending_orders();

        $this->validation->set_rules('customer_name', 'customer_name', 'required|min_length[4]|max_length[255]');
        $this->validation->set_rules('delivery', 'delivery', 'required');
        if (post('delivery') === 'omniva') {
            // Pickup point (ZIP) is mandatory for Omniva delivery.
            $this->validation->set_rules('address', 'address', 'required|min_length[5]|max_length[255]');
        }
        $this->validation->set_rules('phone', 'phone', 'required|min_length[8]|max_length[15]');
        $this->validation->set_rules('email', 'email', 'required|valid_email');
        $this->validation->set_rules('sutikimas', 'sutikimas', 'required');

        $result = $this->validation->run();

        if ($result === true) {

            // Re-check stock before taking payment — the cart may be stale or an
            // item may have sold out since it was added.
            $stock_errors = $this->_cart_stock_errors($cart);
            if (!empty($stock_errors)) {
                set_flashdata('Atnaujinkite krepšelį: ' . implode(' ', $stock_errors));
                redirect('products/cart');
            }

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
                $entry   = $cart[$product->id];
                $qty     = is_array($entry) ? (int) $entry['qty'] : (int) $entry;
                $vid     = is_array($entry) ? ($entry['variant_id'] ?? null) : null;
                $effective_price = ($product->discount_price > 0) ? (float)$product->discount_price : (float)$product->price;
                $total_amount += $effective_price * $qty;
                $order_data = [
                    'order_id'   => $order_id,
                    'product_id' => $product->id,
                    'variant_id' => $vid,
                    'quantity'   => $qty,
                    'price'      => $effective_price,
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
                // Payment init failed — don't leave a dangling 'pending' order.
                // Mark it failed and keep the cart so the customer can retry.
                $this->model->update($order_id, ['status' => 'failed'], 'products_orders');
                set_flashdata('Nepavyko inicijuoti mokėjimo. Bandykite dar kartą arba susisiekite su mumis.');
                redirect('products/cart');
            }

        }

        $this->checkout();

    }

    /**
     * Return human-readable messages for any cart line that exceeds available
     * stock (variant stock when a variant is chosen, otherwise product stock).
     */
    private function _cart_stock_errors(array $cart): array {
        $errors = [];

        foreach ($cart as $product_id => $entry) {
            $qty = is_array($entry) ? (int) ($entry['qty'] ?? 0) : (int) $entry;
            $vid = is_array($entry) ? ($entry['variant_id'] ?? null) : null;
            if ($qty < 1) {
                continue;
            }

            $product = $this->model->get_where((int) $product_id, 'products');
            $name = $product ? $product->name : ('#' . $product_id);

            if ($vid) {
                $variant = $this->model->get_one_where('id', (int) $vid, 'products_variants');
                $available = ($variant && (int) $variant->is_active === 1) ? (int) $variant->stock : 0;
                if ($variant) {
                    $name .= ' (' . $variant->option_value . ')';
                }
            } else {
                $available = $product ? (int) $product->in_stock : 0;
            }

            if ($qty > $available) {
                $errors[] = ($available <= 0)
                    ? $name . ' – nebėra sandėlyje.'
                    : $name . ' – liko tik ' . $available . ' vnt.';
            }
        }

        return $errors;
    }

    /**
     * Mark never-paid orders as cancelled after 24h so abandoned checkouts don't
     * pile up as 'pending' forever. Runs opportunistically at checkout time.
     */
    private function _expire_stale_pending_orders(): void {
        $this->model->query_bind(
            "UPDATE products_orders
             SET status = 'cancelled'
             WHERE status = 'pending'
             AND created_at < (NOW() - INTERVAL 24 HOUR)",
            []
        );
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
            "SELECT product_id, variant_id, quantity FROM products_orders_items WHERE order_id = ?",
            [$order_id],
            'object'
        );

        foreach ($items as $item) {
            if (!empty($item->variant_id)) {
                $this->model->query_bind(
                    "UPDATE products_variants SET stock = GREATEST(0, stock - ?) WHERE id = ?",
                    [$item->quantity, $item->variant_id]
                );
            } else {
                $this->model->query_bind(
                    "UPDATE products SET in_stock = GREATEST(0, in_stock - ?) WHERE id = ?",
                    [$item->quantity, $item->product_id]
                );
            }
        }

        $this->_send_order_email($order_id);
    }

    private function _send_order_email(int $order_id): void {
        $order = $this->model->get_one_where('id', $order_id, 'products_orders');
        if (!$order || empty($order->email)) {
            return;
        }

        $items = $this->model->query_bind(
            "SELECT p.name, i.quantity, i.price
             FROM products_orders_items i
             JOIN products p ON i.product_id = p.id
             WHERE i.order_id = ?",
            [$order_id],
            'object'
        );

        $total = 0;
        $items_html = '';
        foreach ($items as $item) {
            $line_total = $item->quantity * $item->price;
            $total += $line_total;
            $items_html .= '<tr>'
                . '<td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($item->name) . '</td>'
                . '<td style="padding:8px;border-bottom:1px solid #eee;text-align:center;">' . (int)$item->quantity . '</td>'
                . '<td style="padding:8px;border-bottom:1px solid #eee;text-align:right;">' . number_format((float)$item->price, 2) . ' &euro;</td>'
                . '<td style="padding:8px;border-bottom:1px solid #eee;text-align:right;">' . number_format($line_total, 2) . ' &euro;</td>'
                . '</tr>';
        }

        $delivery_label = ($order->delivery === 'omniva')
            ? 'Omniva paštomatas: ' . htmlspecialchars($order->address)
            : 'Atsiėmimas';

        $html = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;color:#333;max-width:600px;margin:0 auto;">'
            . '<h2 style="color:#1a1a1a;">Ačiū už užsakymą, ' . htmlspecialchars($order->customer_name) . '!</h2>'
            . '<p>Jūsų užsakymas <strong>#' . $order_id . '</strong> patvirtintas ir apmokėtas.</p>'
            . '<table style="width:100%;border-collapse:collapse;margin:16px 0;">'
            . '<thead><tr style="background:#f5f5f5;">'
            . '<th style="padding:8px;text-align:left;">Prekė</th>'
            . '<th style="padding:8px;text-align:center;">Kiekis</th>'
            . '<th style="padding:8px;text-align:right;">Kaina</th>'
            . '<th style="padding:8px;text-align:right;">Suma</th>'
            . '</tr></thead>'
            . '<tbody>' . $items_html . '</tbody>'
            . '</table>'
            . '<p style="text-align:right;font-size:1.1em;"><strong>Viso: ' . number_format($total, 2) . ' &euro;</strong></p>'
            . '<p><strong>Pristatymas:</strong> ' . $delivery_label . '</p>'
            . '<p style="color:#555;">Klausimų? Rašykite: <a href="mailto:info@banglente.lt">info@banglente.lt</a></p>'
            . '</body></html>';

        $payload = [
            'sender'      => ['name' => 'Banglente', 'email' => 'info@banglente.lt'],
            'to'          => [['email' => $order->email, 'name' => $order->customer_name]],
            'subject'     => 'Užsakymo patvirtinimas #' . $order_id,
            'htmlContent' => $html,
        ];

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'api-key: ' . constant('BREVO_API'),
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);
        curl_exec($ch);
        curl_close($ch);
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
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => [
                'Authorization: Basic ' . base64_encode($api_username . ':' . $auth_key),
                'Content-Type: application/json'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_POSTFIELDS     => json_encode($payload),
        ]);
        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_error($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['payment_link'])) {
            return $result['payment_link'];
        }

        // Log why the link failed so production issues (rotated credentials,
        // rejected customer_url, gateway downtime) are diagnosable instead of
        // failing silently. The caller handles the false return gracefully.
        error_log(sprintf(
            'EveryPay link failed for ORDER-%s: http=%s curl_err=%s response=%s',
            $order_id,
            $http_code,
            $curl_err !== '' ? $curl_err : 'none',
            is_string($response) && $response !== '' ? $response : '(no body)'
        ));

        return false;
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
        $is_rerender = ($submit !== ''); // came back here from a failed submit()

        if (!$is_rerender && $update_id > 0) {
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
            $data['headline'] = 'Update Product Record';
            $data['cancel_url'] = BASE_URL.'products/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Product Record';
            $data['cancel_url'] = BASE_URL.'products/manage';
        }

        // Categories + variants: repopulate from POST on a validation re-render so
        // the admin's unsaved edits survive; otherwise load the saved record state.
        if ($is_rerender) {
            $posted_cats = post('categories');
            $data['selected_categories'] = is_array($posted_cats) ? $posted_cats : [];
            $data['variants'] = $this->_normalize_posted_variants(post('variants'));
        } elseif ($update_id > 0) {
            $rows = $this->model->query_bind(
                "SELECT category_id FROM products_items_categories WHERE product_id = ?",
                [$update_id], 'object'
            );
            $selected = [];
            foreach ($rows as $row) {
                $selected[] = $row->category_id;
            }
            $data['selected_categories'] = $selected;
            $data['variants'] = $this->model->query_bind(
                "SELECT id, option_name, option_value, stock FROM products_variants WHERE product_id = ? ORDER BY id",
                [$update_id], 'object'
            ) ?: [];
        } else {
            $data['selected_categories'] = [];
            $data['variants'] = [];
        }

        $data['form_location'] = BASE_URL.'products/submit/'.$update_id;
        $data['view_file'] = 'create';
        $this->template('admin_area', $data);
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

        $data['rows'] = $this->_attach_categories($this->reduce_rows($all_rows));
        $data['selected_per_page'] = $this->get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'products';
        $data['view_file'] = 'manage';
        $this->template('admin_area', $data);
    }

    /**
     * Attach category names to the given product rows for display in the
     * manage table. Runs a single query for just the visible (paginated) rows.
     */
    private function _attach_categories(array $rows): array {
        if (empty($rows)) {
            return $rows;
        }

        $ids = array_map(static fn($r) => (int) $r->id, $rows);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT pic.product_id, pc.name
                FROM products_items_categories pic
                JOIN products_categories pc ON pc.id = pic.category_id
                WHERE pic.product_id IN ($placeholders)
                ORDER BY pc.name";
        $links = $this->model->query_bind($sql, $ids, 'object');

        $by_product = [];
        foreach ($links as $link) {
            $by_product[(int) $link->product_id][] = $link->name;
        }

        foreach ($rows as $row) {
            $row->categories = $by_product[(int) $row->id] ?? [];
        }

        return $rows;
    }

    public function orders(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';
        $allowed_statuses = ['pending', 'paid', 'failed', 'cancelled'];

        if ($status_filter && in_array($status_filter, $allowed_statuses)) {
            $all_rows = $this->model->query_bind(
                "SELECT * FROM products_orders WHERE status = ? ORDER BY id DESC",
                [$status_filter],
                'object'
            );
            $data['headline'] = 'Orders: ' . ucfirst($status_filter);
        } else {
            $status_filter = '';
            $all_rows = $this->model->query(
                "SELECT * FROM products_orders ORDER BY id DESC",
                'object'
            );
            $data['headline'] = 'Manage Orders';
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->get_limit();
        $pagination_data['pagination_root'] = 'products/orders';
        $pagination_data['record_name_plural'] = 'orders';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->reduce_rows($all_rows);
        $data['selected_per_page'] = $this->get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['status_filter'] = $status_filter;
        $data['view_module'] = 'products';
        $data['view_file'] = 'orders';
        $this->template('admin_area', $data);
    }

    public function show_order(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $order_id = (int) segment(3);
        if ($order_id === 0) {
            redirect('products/orders');
        }

        $order = $this->model->get_one_where('id', $order_id, 'products_orders');
        if (!$order) {
            redirect('products/orders');
        }

        $items = $this->model->query_bind(
            "SELECT p.name, p.image, i.quantity, i.price
             FROM products_orders_items i
             JOIN products p ON i.product_id = p.id
             WHERE i.order_id = ?",
            [$order_id],
            'object'
        );

        $data['order'] = $order;
        $data['items'] = $items;
        $data['headline'] = 'Order #' . $order_id;
        $data['view_module'] = 'products';
        $data['view_file'] = 'show_order';
        $this->template('admin_area', $data);
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
            $data['filezone_settings'] = $this->_init_filezone_settings();
            $data['view_file'] = 'show';
            $this->template('admin_area', $data);
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

        // categories[] is an array field; the validation library's scalar rules
        // can't handle it (required would trim() an array), so check it manually
        // and push the error into the same store the library uses.
        $categories = post('categories');
        if (empty($categories) || !is_array($categories)) {
            $this->validation->form_submission_errors['categories'][] = 'You must select at least one category.';
        }

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
        // discount_price is optional; 0 (or blank) means "no discount", so only
        // require it to be numeric — greater_than[0] would reject the common 0 case.
        $this->validation->set_rules('discount_price', 'discount_price', 'numeric');
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

    /**
     * Sync a product's variants against the posted variant rows.
     * Updates existing rows (preserving each variant's stock), inserts new ones,
     * and deletes only rows the admin removed in the form — so editing a product
     * no longer wipes its variants. Posted ids are validated against this
     * product's own variants to prevent cross-product tampering.
     */
    private function save_product_variants(int $product_id): void {
        $variants = post('variants');

        $existing = $this->model->query_bind(
            "SELECT id FROM products_variants WHERE product_id = ?",
            [$product_id], 'object'
        ) ?: [];
        $existing_ids = array_map(static fn($r) => (int) $r->id, $existing);

        $kept_ids = [];

        if (!empty($variants) && is_array($variants)) {
            foreach ($variants as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $option_name  = trim((string) ($row['option_name'] ?? ''));
                $option_value = trim((string) ($row['option_value'] ?? ''));
                $stock        = max(0, (int) ($row['stock'] ?? 0));
                $vid          = (int) ($row['id'] ?? 0);

                // option_name is constrained to the DB enum; skip blank/invalid rows.
                if (!in_array($option_name, ['color', 'size'], true) || $option_value === '') {
                    continue;
                }

                if ($vid > 0 && in_array($vid, $existing_ids, true)) {
                    $this->model->update($vid, [
                        'option_name'  => $option_name,
                        'option_value' => $option_value,
                        'stock'        => $stock,
                    ], 'products_variants');
                    $kept_ids[] = $vid;
                } else {
                    $kept_ids[] = $this->model->insert([
                        'product_id'   => $product_id,
                        'option_name'  => $option_name,
                        'option_value' => $option_value,
                        'stock'        => $stock,
                        'is_active'    => 1,
                    ], 'products_variants');
                }
            }
        }

        // Remove variants the admin deleted from the form.
        foreach ($existing_ids as $eid) {
            if (!in_array($eid, $kept_ids, true)) {
                $this->model->query_bind("DELETE FROM products_variants WHERE id = ?", [$eid]);
            }
        }
    }

    private function update_product(int $id, array $data): void {
        $this->model->update($id, $data, 'products');

        // Category links are fully rebuilt by save_product_categories(); variants
        // are synced in place by save_product_variants() (no blanket delete here,
        // which previously wiped variants on every edit).
        $this->model->query_bind("DELETE FROM products_items_categories WHERE product_id = ?", [$id]);
    }
    
    private function create_product(array $data): int {
        return $this->model->insert($data, 'products');
    }

    /**
     * Shape posted variant rows into objects matching the DB row format, so the
     * create view can re-render the admin's variants after a failed validation.
     */
    private function _normalize_posted_variants($posted): array {
        if (empty($posted) || !is_array($posted)) {
            return [];
        }

        $out = [];
        foreach ($posted as $row) {
            if (!is_array($row)) {
                continue;
            }
            $out[] = (object) [
                'id'           => (int) ($row['id'] ?? 0),
                'option_name'  => $row['option_name'] ?? '',
                'option_value' => $row['option_value'] ?? '',
                'stock'        => $row['stock'] ?? '',
            ];
        }
        return $out;
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

    // Settings for the multi-picture gallery (trongate_filezone).
    // The single `image` column stays the cover; these are extra pictures.
    function _init_filezone_settings() {
        $data['targetModule'] = 'products';
        $data['destination'] = 'products_pictures';
        $data['max_file_size'] = 1200;
        $data['max_width'] = 1500;
        $data['max_height'] = 1500;
        $data['thumbnail_dir'] = 'thumbnails';
        $data['thumbnail_max_width'] = 320;
        $data['thumbnail_max_height'] = 320;
        $data['upload_to_module'] = true;
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