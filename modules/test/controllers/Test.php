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