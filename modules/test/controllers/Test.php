<?php
class Test extends Trongate {

    function index() {

        $this->module('trongate_tokens');
        $token = $this->trongate_tokens->_attempt_get_valid_token();

        if ($token === false) {
            redirect('tg-admin');
        }
        $data['token'] = $token;
        $data['view_file'] = 'test';
        $this->template('public', $data);

    }

    function port() {

        $this->module('trongate_tokens');
        $token = $this->trongate_tokens->_attempt_get_valid_token();
        $data['token'] = $token;
        $data['vanduo'] = $this->vanduo();
        $data['view_file'] = 'port';
        $this->template('public', $data);

    }

    // Returns 4-day forecast JSON; cached 1 hour
    public function surf_forecast(): void {
        header('Content-Type: application/json');

        $cache_file = sys_get_temp_dir() . '/molas_surf_forecast.json';
        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < 3600) {
            echo file_get_contents($cache_file);
            die();
        }

        $days = $this->_build_forecast_days();

        if (!empty($days)) {
            $ratings = $this->_ai_forecast_ratings($days);
            foreach ($days as $i => &$day) {
                $day['rating'] = $ratings[$i]['rating'] ?? 'nežinoma';
                $day['badge']  = $ratings[$i]['label']  ?? '';
            }
            unset($day);
        }

        $result = ['days' => $days, 'fetched_at' => date('H:i')];
        $json   = json_encode($result, JSON_UNESCAPED_UNICODE);
        file_put_contents($cache_file, $json);
        echo $json;
        die();
    }

    private function _build_forecast_days(): array {
        $marine  = $this->_curl_json(
            'https://marine-api.open-meteo.com/v1/marine'
            . '?latitude=55.73&longitude=21.1'
            . '&hourly=wave_height,wave_period,wave_direction'
            . '&forecast_days=4&timezone=Europe%2FVilnius'
        );
        $weather = $this->_curl_json(
            'https://api.open-meteo.com/v1/forecast'
            . '?latitude=55.73&longitude=21.1'
            . '&hourly=wind_speed_10m,wind_direction_10m,temperature_2m'
            . '&forecast_days=4&timezone=Europe%2FVilnius&wind_speed_unit=ms'
        );

        if (!$marine || !$weather) return [];

        $mh = $marine['hourly'];
        $wh = $weather['hourly'];

        // Group hourly records by date
        $by_date = [];
        foreach ($mh['time'] as $i => $time) {
            $date = substr($time, 0, 10);
            $hour = (int)substr($time, 11, 2);
            $by_date[$date][] = [
                'hour'        => $hour,
                'wave_height' => $mh['wave_height'][$i],
                'wave_period' => $mh['wave_period'][$i],
                'wind_speed'  => $wh['wind_speed_10m'][$i]    ?? null,
                'wind_dir'    => $wh['wind_direction_10m'][$i] ?? null,
                'temp'        => $wh['temperature_2m'][$i]     ?? null,
            ];
        }

        $labels = ['Šiandien', 'Rytoj', 'Poryt'];
        $days   = [];
        $idx    = 0;

        foreach ($by_date as $date => $all_hours) {
            // Daytime slice 8–20 for stats; full 24h for sparkline
            $day = array_values(array_filter($all_hours, fn($h) => $h['hour'] >= 8 && $h['hour'] <= 20));
            if (empty($day)) $day = $all_hours;

            $wave_h   = array_column($day, 'wave_height');
            $wind_s   = array_column($day, 'wind_speed');
            $wind_d   = array_column($day, 'wind_dir');
            $temps    = array_column($day, 'temp');

            // 2-hourly sparkline points (hours 6,8,10,12,14,16,18,20)
            $spark = [];
            foreach ($all_hours as $h) {
                if ($h['hour'] >= 6 && $h['hour'] <= 20 && $h['hour'] % 2 === 0) {
                    $spark[] = round($h['wave_height'], 2);
                }
            }

            // Dominant wind direction (circular mean)
            $dom_dir = null;
            if (!empty($wind_d)) {
                $sin = array_sum(array_map(fn($d) => sin(deg2rad($d)), $wind_d)) / count($wind_d);
                $cos = array_sum(array_map(fn($d) => cos(deg2rad($d)), $wind_d)) / count($wind_d);
                $dom_dir = $this->_deg_to_compass((float)rad2deg(atan2($sin, $cos)));
            }

            $days[] = [
                'date'       => $date,
                'label'      => $labels[$idx] ?? date('l', strtotime($date)),
                'wave_min'   => round(min($wave_h), 1),
                'wave_max'   => round(max($wave_h), 1),
                'wave_period'=> round(array_sum(array_column($day, 'wave_period')) / count($day), 1),
                'wind_avg'   => $wind_s ? round(array_sum($wind_s) / count($wind_s), 1) : null,
                'wind_max'   => $wind_s ? round(max($wind_s), 1) : null,
                'wind_dir'   => $dom_dir,
                'temp_max'   => $temps ? (int)round(max($temps)) : null,
                'spark'      => $spark,
            ];
            $idx++;
        }

        return $days;
    }

    private function _ai_forecast_ratings(array $days): array {
        $lines = '';
        foreach ($days as $i => $d) {
            $lines .= 'Diena ' . ($i + 1) . " ({$d['date']}): "
                . "bangos {$d['wave_min']}–{$d['wave_max']}m, "
                . "periodas {$d['wave_period']}s, "
                . "vėjas iki {$d['wind_max']}m/s iš {$d['wind_dir']}\n";
        }

        $n       = count($days);
        $payload = json_encode([
            'model'      => 'claude-haiku-4-5-20251001',
            'max_tokens' => 220,
            'system'     => "Tu esi banglenčių treneris prie Baltijos jūros Klaipėdoje. "
                          . "Įvertink {$n} dienas ir grąžink TIKTAI JSON masyvą su {$n} objektais: "
                          . '[{"rating":"puikios"|"geros"|"vidutinės"|"blogos","label":"iki 4 žodžių lt"},...]. '
                          . 'Jokio kito teksto. Sąlygas vertink pagal bangų aukštį, periodą ir vėjo greitį/kryptį. Naudok kreipinius Serfingas ir serferis',
            'messages'   => [['role' => 'user', 'content' => $lines]],
        ]);

        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key: ' . constant('ANTHROPIC_API_KEY'),
                'anthropic-version: 2023-06-01',
            ],
            CURLOPT_POSTFIELDS => $payload,
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);

        if (!$raw) return [];
        $api  = json_decode($raw, true);
        $text = preg_replace('/```[a-z]*\n?|\n?```/', '', trim($api['content'][0]['text'] ?? '[]'));
        return json_decode($text, true) ?? [];
    }

    private function _curl_json(string $url): ?array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);
        if (!$raw) return null;
        return json_decode($raw, true) ?: null;
    }

    private function _deg_to_compass(float $deg): string {
        // Lithuanian: Š=North, R=East, P=South, V=West
        $dirs = ['Š', 'ŠR', 'R', 'PR', 'P', 'PV', 'V', 'ŠV'];
        return $dirs[((int)round(fmod($deg + 360, 360) / 45)) % 8];
    }

    // Returns JSON surf-condition rating; cached 15 min to spare APIs + AI cost
    public function surf_rating(): void {
        header('Content-Type: application/json');

        $cache_file = sys_get_temp_dir() . '/molas_surf_rating.json';
        $cache_ttl  = 900; // 15 minutes

        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_ttl) {
            echo file_get_contents($cache_file);
            die();
        }

        $wind_speed     = $this->_port_metric('wind_speed');
        $wind_direction = $this->_port_metric('wind_direction');
        $waves          = $this->_open_meteo_waves();

        $result = $this->_ai_surf_rating($wind_speed, $wind_direction, $waves);
        $result['fetched_at'] = date('H:i');

        $json = json_encode($result);
        file_put_contents($cache_file, $json);
        echo $json;
        die();
    }

    private function _port_metric(string $method): ?float {
        $ch = curl_init('https://portofklaipeda.lt/wp-json/api/meteo_data?method=' . $method);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 6,
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);

        if (!$raw) return null;
        $rows = json_decode($raw, true);
        if (!is_array($rows) || empty($rows)) return null;
        $last = end($rows);
        return isset($last[1]) ? round((float)$last[1], 1) : null;
    }

    private function _open_meteo_waves(): array {
        // Melnragė: 55.73°N, 21.1°E
        $url = 'https://marine-api.open-meteo.com/v1/marine'
             . '?latitude=55.73&longitude=21.1'
             . '&current=wave_height,wave_period,wave_direction,wind_wave_height,swell_wave_height'
             . '&timezone=Europe%2FVilnius';

        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 6]);
        $raw = curl_exec($ch);
        curl_close($ch);

        if (!$raw) return [];
        $data = json_decode($raw, true);
        return $data['current'] ?? [];
    }

    private function _ai_surf_rating(?float $wind_speed, ?float $wind_direction, array $waves): array {
        $wh = $waves['wave_height']       ?? null;
        $wp = $waves['wave_period']       ?? null;
        $wd = $waves['wave_direction']    ?? null;

        $ctx = "Melnragė, Klaipėda — dabartinės sąlygos:\n";
        if ($wind_speed     !== null) $ctx .= "- Vėjo greitis: {$wind_speed} m/s\n";
        if ($wind_direction !== null) $ctx .= "- Vėjo kryptis: {$wind_direction}°\n";
        if ($wh             !== null) $ctx .= "- Bangų aukštis: {$wh} m\n";
        if ($wp             !== null) $ctx .= "- Bangų periodas: {$wp} s\n";
        if ($wd             !== null) $ctx .= "- Bangų kryptis: {$wd}°\n";

        $payload = json_encode([
            'model'      => 'claude-haiku-4-5-20251001',
            'max_tokens' => 120,
            'system'     => 'Tu esi banglenčių treneris prie Baltijos jūros. '
                          . 'Įvertink sąlygas ir atsakyk TIKTAI JSON: '
                          . '{"rating":"puikios"|"geros"|"vidutinės"|"blogos","label":"iki 5 žodžių lietuviškai","advice":"vienas sakinys lietuviškai"}. '
                          . 'Jokio kito teksto.',
            'messages'   => [['role' => 'user', 'content' => $ctx]],
        ]);

        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key: ' . constant('ANTHROPIC_API_KEY'),
                'anthropic-version: 2023-06-01',
            ],
            CURLOPT_POSTFIELDS => $payload,
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);

        $fallback = ['rating' => 'nežinoma', 'label' => 'Duomenys nepasiekiami', 'advice' => 'Susisiekite su klubu dėl sąlygų.'];
        if (!$raw) return array_merge($fallback, compact('wind_speed', 'wind_direction') + ['wave_height' => $wh, 'wave_period' => $wp]);

        $api  = json_decode($raw, true);
        $text = $api['content'][0]['text'] ?? '{}';
        // Strip any markdown fences the model might add
        $text = preg_replace('/```[a-z]*\n?|\n?```/', '', trim($text));
        $ai   = json_decode($text, true) ?? [];

        return [
            'rating'          => $ai['rating']  ?? $fallback['rating'],
            'label'           => $ai['label']   ?? $fallback['label'],
            'advice'          => $ai['advice']  ?? $fallback['advice'],
            'wind_speed'      => $wind_speed,
            'wind_direction'  => $wind_direction,
            'wave_height'     => $wh,
            'wave_period'     => $wp,
        ];
    }

    function baltija() {
        $data['view_file'] = 'baltija';
        $this->template('public', $data);
    }

    function dragdrop() {
        $data['view_file'] = 'dragdrop';
        $this->template('public', $data);
    }

    function vanduo() {

        // API URL for Klaipėdos Jūrų Uosto VMS
        $url = 'https://api.meteo.lt/v1/hydro-stations/klaipedos-juru-uosto-vms/observations/measured/latest';

        // Fetch data
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Use true in production
        $response = curl_exec($ch);
        curl_close($ch);

        // Decode JSON
        $data = json_decode($response, true);

        // Get latest observation
        $latestTemp = null;
        $latestTime = null;

        if ($data && isset($data['observations'])) {
            // Get the last entry (latest time is at the end)
            $latest = end($data['observations']);
            $latestTemp = $latest['waterTemperature'];
            $latestTime = $latest['observationTimeUtc'];
        }

        return $latestTemp;

    }

    // ------------- INSTAGRAM PIC UPLOAD TO SERVER -------------
    // ----------------------------------------------------------
    function instagram() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $sql = "SELECT 
        p.id AS post_id,
        p.caption,
        p.prompt,
        p.media_type,
        p.status,
        GROUP_CONCAT(m.file_url ORDER BY m.position SEPARATOR ',') AS file_urls
        FROM instagram_posts AS p
        JOIN instagram_media AS m ON m.post_id = p.id
        GROUP BY p.id";

        $data['posts'] = $this->model->query($sql, 'object');
        $data['view_file'] = 'instagram';
        $this->template('admin_area', $data);
    }

    function show_all(): void{

        $sql = "SELECT 
                    p.id AS post_id,
                    p.caption,
                    p.prompt,
                    p.media_type,
                    p.status,
                    GROUP_CONCAT(m.file_url ORDER BY m.position SEPARATOR ',') AS file_urls
                FROM instagram_posts AS p
                JOIN instagram_media AS m ON m.post_id = p.id
                GROUP BY p.id";

        $data['posts'] = $this->model->query($sql, 'object');

        $this->view('show', $data);
    }

    private function _init_picture_settings() { 
        $picture_settings['max_file_size'] = 2000;
        $picture_settings['max_width'] = 2200;
        $picture_settings['max_height'] = 2200;
        $picture_settings['resized_max_width'] = 950;
        $picture_settings['resized_max_height'] = 950;
        $picture_settings['destination'] = 'instagram_pics';
        $picture_settings['target_column_name'] = 'file_url';
        $picture_settings['thumbnail_dir'] = 'instagram_pics_thumbnails';
        $picture_settings['thumbnail_max_width'] = 120;
        $picture_settings['thumbnail_max_height'] = 120;
        $picture_settings['upload_to_module'] = true;
        $picture_settings['make_rand_name'] = false;
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

        $prompt = post('prompt');

        $picture_settings = $this->_init_picture_settings();
        extract($picture_settings);
    
        $validation_str = 'allowed_types[gif,webp,jpg,jpeg,png]|max_size['.$max_file_size.']|max_width['.$max_width.']|max_height['.$max_height.']';
        $this->validation->set_rules('picture', 'item picture', $validation_str);

        $result = $this->validation->run();

        if ($result == true) {
            $config['destination'] = $destination;
            $config['max_width'] = $resized_max_width;
            $config['max_height'] = $resized_max_height;
    
            if ($thumbnail_dir !== '') {
                $config['thumbnail_dir'] = $thumbnail_dir;
                $config['thumbnail_max_width'] = $thumbnail_max_width;
                $config['thumbnail_max_height'] = $thumbnail_max_height;
            }
    
            $config['upload_to_module'] = (!isset($picture_settings['upload_to_module']) ? false : $picture_settings['upload_to_module']);
            $config['make_rand_name'] = $picture_settings['make_rand_name'] ?? false;

            $file_info = $this->upload_picture($config);

            // Prepare file URL
            $file_url = 'https://www.surfclub.lt/test_module/instagram_pics/' . $file_info['file_name'];

            $post_data = [
                'caption' => '', // or wait for OpenAI to generate it
                'media_type' => 'image', // or 'carousel' / 'video' based on user input
                'status' => 'not ready',
                'prompt' => $prompt,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $update_id = $this->model->insert($post_data, 'instagram_posts');

            // Prepare DB data
            $data = [
                'post_id' => $update_id, // post this media belongs to
                'file_url' => $file_url,
                'media_format' => 'image',
                'position' => 0 // you can change this later
            ];
            $this->model->insert($data, 'instagram_media');
    
            $flash_msg = 'The picture was successfully uploaded';
            set_flashdata($flash_msg);
            redirect($_SERVER['HTTP_REFERER']);
    
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    function submit_upload_file() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();
    
        if ($_FILES['my_files']['name'][0] == '') {
            redirect($_SERVER['HTTP_REFERER']);
        }

        $prompt = post('prompt');
    
        $validation_str = 'allowed_types[gif,webp,jpg,jpeg,png,mp4,avi,png,webm]';
        $this->validation->set_rules('my_files[]', 'file', $validation_str);

        $result = $this->validation->run();


        if ($result == true) {

            $is_carousel = count($_FILES["my_files"]['name']) > 1;
            $media_type = $is_carousel ? 'carousel' : explode('/', $_FILES["my_files"]['type'][0])[0];

            $post_data = [
                'caption' => '', // or wait for OpenAI to generate it
                'media_type' => $media_type, // 'image' or 'video' based on user input
                'status' => 'not ready',
                'prompt' => $prompt,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $update_id = $this->model->insert($post_data, 'instagram_posts');
            $destination = 'public_html/files/social/';
            
            foreach ($_FILES["my_files"]["error"] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES["my_files"]["tmp_name"][$key];
                    // basename() may prevent filesystem traversal attacks;
                    // further validation/sanitation of the filename may be appropriate
                    $name = basename($_FILES["my_files"]["name"][$key]);

                    $move_to_name =  APPPATH . $destination . $name;

                    if ( ! move_uploaded_file($tmp_name, $move_to_name)) {
                        print_r($move_to_name);
                        exit("Can't move uploaded file");
                    };

                    $file_url = BASE_URL . 'files/social/' . $name;

                    $media_format = explode('/', $_FILES["my_files"]['type'][$key])[0];

                    // Prepare DB media data
                    $data = [
                        'post_id' => $update_id, // post this media belongs to
                        'file_url' => $file_url,
                        'media_format' => $media_format,
                        'position' => $key // you can change this later
                    ];
                    $this->model->insert($data, 'instagram_media');
                }
            }
    
            $flash_msg = 'The files was successfully uploaded';
            set_flashdata($flash_msg);
            redirect($_SERVER['HTTP_REFERER']);
    
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    // ----- GENERATE AI POST -----
    function generate_make_webhook() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $update_id =  (int)segment(3);
    
        $data = array(
            'submit_form' => 1,
            'validation' => 'lkj@ids(&%54j3233kl__-o233',
            'update_id' => $update_id,
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://hook.eu2.make.com/nx5pk4y2vxehqxn3auv8mvmhizwqdxtb");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $output = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);
        set_flashdata("The post generated succesfully");

        redirect('test/instagram');

    }
    // ----- PUBLISH AI POST -----
    function publish_make_webhook() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $update_id =  (int)segment(3);
        $data = array(
            'submit_form' => 1,
            'validation' => 'lkj@ids(&%54j3233kl__-o233',
            'update_id' => $update_id,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://hook.eu2.make.com/11r14twow6z4jea4u0o24pihdalyqn74");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $output = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);
        set_flashdata("The post generated succesfully");

        redirect('test/instagram');

    }

    public function submit_delete() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit');
        $params['update_id'] = (int)segment(3);

        if((($submit == 'Yes - Delete Now') || (from_trongate_mx() === true)) && ($params['update_id']>0)) {
            // Delete media record from instagram_media table
            $sql = "DELETE FROM instagram_media WHERE post_id = :update_id";
            $this->model->query_bind($sql, $params);
            // Delete post record from instagram_posts table
            $this->model->delete($params['update_id'], 'instagram_posts');

            set_flashdata('Post was successfully deleted');
            redirect('test/instagram');
        }

    }

    public function edit() {
        $update_id = (int)segment(3);
        $data['product'] = $this->model->get_one_where('id', $update_id, 'instagram_posts');
        $this->view('insta_edit', $data);
    }

    public function submit_edit() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();
        $update_id = (int)segment(3);
        $data['caption'] = post('caption');
        $this->model->update($update_id, $data, 'instagram_posts');
        $flash_msg = 'The post was successfully updated';
        set_flashdata($flash_msg);
        redirect('test/instagram');
    }

    function delfi_cam() {
        $target_url = 'https://www.surfline.com/surf-report/melnrage/6026e98876fdd97f919ce405';
        $response_body = file_get_contents($target_url);
        echo $response_body;
    }

    function submit_gallery_upload() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if ($_FILES['my_file']['name'] == '') {
            redirect($_SERVER['HTTP_REFERER']);
        }

        //PLEASE NOTE: max_size is in kilobytes
        $validation_str = 'allowed_types[gif,jpg,jpeg,png,zip]|max_size[2000]';
        $this->validation->set_rules('my_file', 'file', $validation_str);

        $result = $this->validation->run();

        if ($result == true) {

            //upload the file
            $config['destination'] = '../public/files';
            $config['make_rand_name'] = false;

            $file_info = $this->upload_file($config);

            //set some flashdata
            set_flashdata('Your file ('.$file_info['file_name'].') was successfully uploaded');

            //Job done! Send the user to another page...
            $target_url = str_replace('/submit_upload', '/show', current_url());
            redirect($target_url);

        } else {
            //validation error! Present the form again.
            $this->upload();
        }
    }
}