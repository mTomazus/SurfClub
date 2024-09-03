<?php
class News extends Trongate {

    private $module_title = 'News';
    private $include_world_clocks = true;
    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100); 
    private $template_to_use = 'public';
    private $show_world_clocks = false;
    private $allow_mass_delete = false;

    function display() {
        $params['code'] = segment(3);

        $code_len = strlen($params['code']);
        settype($code_len, 'int');

        if ($code_len !== 6) {
            redirect('news');
        }

        $sql = 'SELECT * from news where code = :code and published = 1';
        $rows = $this->model->query_bind($sql, $params, 'object');
        
        if (!isset($rows[0])) {
            redirect('news');
        } else {
            $rows = $this->_add_picture_paths($rows);
        }

        $data = (array) $rows[0];
        $data['module_title'] = $this->module_title;
        $data['article_body'] = str_replace('[youtube]', '<div class="youtube-video" style="display: none;">', $data['article_body']);
        $data['article_body'] = str_replace('[/youtube]', '</div>', $data['article_body']);
        $data['articles'] = $this->_fetch_other_articles($params['code']);
        $data['view_module'] = 'news';
        $data['view_file'] = 'article';
        $this->template($this->template_to_use, $data);
    }

    function _fetch_other_articles($code) {
        $params['code'] = $code;
        $sql = 'SELECT * from news where code != :code and published = 1 order by date_and_time desc limit 0,15';
        $rows = $this->model->query_bind($sql, $params, 'object'); 
        $rows = $this->_add_picture_paths($rows);
        return $rows;       
    }

    function index() {
        $params['published'] = 1;
        $sql = 'SELECT * from news 
                WHERE published = :published 
                ORDER BY date_and_time desc';

        $articles = $this->model->query_bind($sql, $params, 'object');
        $data['show_world_clocks'] = $this->show_world_clocks;
        $data['module_title'] = $this->module_title;
        $data['headlines_json'] = $this->_build_headlines_json($articles, 3);
        $data['articles'] = $this->_add_picture_paths($articles);
        $data['view_module'] = 'news';
        $data['view_file'] = 'news';
        $this->template($this->template_to_use, $data);
    }

    function _add_picture_paths($articles) {
        foreach($articles as $key => $value) {
            $picture_path = BASE_URL.'news_module/images/news_pics/'.$value->id.'/'.$value->picture;
            $articles[$key]->picture_path = $picture_path;
            $articles[$key]->article_url = BASE_URL.'news/display/'.$value->code.'/'.$value->url_string;
        }

        return $articles;
    }

    // Pakeiciau picture path truputi

    //function _add_picture_paths($articles) {
    //    foreach($articles as $key => $value) {
    //        $picture_path = BASE_URL.segment(1).'_module/images/'.segment(1).'_pics/'.$value->id.'/'.$value->picture;
    //        $articles[$key]->picture_path = $picture_path;
    //        $articles[$key]->article_url = BASE_URL.'news/display/'.$value->code.'/'.$value->url_string;
    //    }
    //    return $articles;
    //}

    function _build_headlines_json($articles, $skip) {
        $headlines_array = [];
        $counter = 0;
        foreach($articles as $article) {
            $counter++;
            if ($counter > $skip) {
                $headlines_array[] = $article->article_headline;
            }

        }

        $headlines_json = json_encode($headlines_array);
        return $headlines_json;
    }

    function create() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $update_id = segment(3);
        $submit = post('submit');

        if (($submit == '') && (is_numeric($update_id))) {
            $data = $this->_get_data_from_db($update_id);
        } else {
            $data = $this->_get_data_from_post();
        }

        if (is_numeric($update_id)) {
            $data['headline'] = 'Update News Record';
            $data['cancel_url'] = BASE_URL.'news/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New News Record';
            $data['cancel_url'] = BASE_URL.'news/manage';
        }

        $data['form_location'] = BASE_URL.'news/submit/'.$update_id;
        $data['view_file'] = 'create';
        $this->template('admin', $data);
    }

    function search() {
        $this->manage();
    }

    function manage() {

        if (segment(4) !== '') {
            $data['headline'] = 'Search Results';
            $searchphrase = trim($_GET['searchphrase']);
            $params['article_headline'] = '%'.$searchphrase.'%';
            $params['sounds_like'] = '%'.metaphone($searchphrase).'%';

            $sql = 'select * from news
            WHERE article_headline LIKE :article_headline 
            OR sounds_like LIKE :sounds_like 
            ORDER BY date_and_time desc';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
            $all_rows = $this->_remove_youtube_videos($all_rows);

        } else {
            $data['headline'] = 'Manage News';
            $all_rows = $this->model->get('date_and_time desc');
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->_get_limit();
        $pagination_data['pagination_root'] = 'news/'.segment(2);
        $pagination_data['record_name_plural'] = 'news';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->_reduce_rows($all_rows);
        $data['selected_per_page'] = $this->_get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'news';

        if (segment(2) == 'manage') {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed();
            $template_to_use = 'admin';
            $view_file_to_use = 'manage';
        } else {
            $template_to_use = $this->template_to_use;
            $view_file_to_use = 'search_results';
            $data['rows'] = $this->_add_picture_paths($data['rows']);
            $data['rows'] = $this->_reduce_articles($data['rows']);
        }

        $data['view_file'] = $view_file_to_use;
        $data['allow_mass_delete'] = $this->allow_mass_delete;
        $this->template($template_to_use, $data);
    }

    function _remove_youtube_videos($rows) {
        //this gets used on the search results page
        foreach($rows as $key => $value) {
            $article_body = $value->article_body;
            $article_body = str_replace('[youtube]', '<[youtube]', $article_body);
            $article_body = str_replace('[/youtube]', '[youtube]>', $article_body);
            $article_body = strip_tags($article_body);
            $rows[$key]->article_body = $article_body;
        }
        return $rows;
    }

    function _reduce_articles($rows) {
        foreach($rows as $key => $value) {
            $article_body = $value->article_body;
            $rows[$key]->article_body = $this->_limit_text($article_body, 27);
        }

        return $rows;
    }

    function show() {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();
        $update_id = segment(3);

        if ((!is_numeric($update_id)) && ($update_id != '')) {
            redirect('news/manage');
        }

        $data = $this->_get_data_from_db($update_id);
        $data['published'] = ($data['published'] == 1 ? 'yes' : 'no');
        $data['token'] = $token;

        if ($data == false) {
            redirect('news/manage');
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

            $data['article_body_preview'] = $this->_limit_text($data['article_body'], 33);
            $data['update_id'] = $update_id;
            $data['headline'] = 'News Information';
            $data['view_file'] = 'show';
            $this->template('admin', $data);
        }
    }
    
    function _reduce_rows($all_rows) {
        $rows = [];
        $start_index = $this->_get_offset();
        $limit = $this->_get_limit();
        $end_index = $start_index + $limit;

        $count = -1;
        foreach ($all_rows as $row) {
            $count++;
            if (($count>=$start_index) && ($count<$end_index)) {
                $row->published = ($row->published == 1 ? 'yes' : 'no');
                $rows[] = $row;
            }
        }

        return $rows;
    }

    function submit() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit', true);

        if ($submit == 'Submit') {

            $this->validation_helper->set_rules('article_headline', 'Article Headline', 'required|min_length[2]|max_length[255]');
            $this->validation_helper->set_rules('article_body', 'Article Body', 'required|min_length[2]');
            $this->validation_helper->set_rules('date_and_time', 'Date And Time', 'required|valid_datetimepicker_us');

            $result = $this->validation_helper->run();

            if ($result == true) {

                $update_id = segment(3);
                $data = $this->_get_data_from_post();
                $data['url_string'] = strtolower(url_title($data['article_headline']));
                $data['published'] = ($data['published'] == 1 ? 1 : 0);
                $data['date_and_time'] = str_replace(' at ', '', $data['date_and_time']);
                $data['date_and_time'] = date('Y-m-d H:i', strtotime($data['date_and_time']));
                $data['sounds_like'] = metaphone($data['article_headline']);

                if (is_numeric($update_id)) {
                    //update an existing record
                    $this->model->update($update_id, $data, 'news');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    //insert the new record
                    $data['code'] = make_rand_str(6, true);
                    $update_id = $this->model->insert($data, 'news');
                    $flash_msg = 'The record was successfully created';
                }

                set_flashdata($flash_msg);
                redirect('news/show/'.$update_id);

            } else {
                //form submission error
                $this->create();
            }

        }

    }

    function submit_delete() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit');
        $params['update_id'] = segment(3);

        if (($submit == 'Yes - Delete Now') && (is_numeric($params['update_id']))) {
            //delete all of the comments associated with this record
            $sql = 'delete from trongate_comments where target_table = :module and update_id = :update_id';
            $params['module'] = 'news';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'news');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('news/manage');
        }
    }

    function submit_delete_everything() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if (ENV !== 'dev') {
            echo 'Mass delete only works when ENV is set to \'dev\'';
            die();
        }

        if ($this->allow_mass_delete == true) {
            
            //truncate the entire news database table
            $sql = 'truncate news';
            $this->model->query($sql);

            //delete everything inside news_pics folder
            $target_dir = APPPATH.'modules/news/assets/images';
            $this->_rrmdir($target_dir);

            set_flashdata('Everything has been deleted.');
            redirect('news/manage');

        } else {
            redirect('news');
        }    
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

    function _get_limit() {
        if (isset($_SESSION['selected_per_page'])) {
            $limit = $this->per_page_options[$_SESSION['selected_per_page']];
        } else {
            $limit = $this->default_limit;
        }

        return $limit;
    }

    function _get_offset() {
        $page_num = segment(3);

        if (!is_numeric($page_num)) {
            $page_num = 0;
        }

        if ($page_num>1) {
            $offset = ($page_num-1)*$this->_get_limit();
        } else {
            $offset = 0;
        }

        return $offset;
    }

    function _get_selected_per_page() {
        if (!isset($_SESSION['selected_per_page'])) {
            $selected_per_page = $this->per_page_options[1];
        } else {
            $selected_per_page = $_SESSION['selected_per_page'];
        }

        return $selected_per_page;
    }

    function set_per_page($selected_index) {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if (!is_numeric($selected_index)) {
            $selected_index = $this->per_page_options[1];
        }

        $_SESSION['selected_per_page'] = $selected_index;
        redirect('news/manage');
    }

    function _get_data_from_db($update_id) {
        $record_obj = $this->model->get_where($update_id, 'news');

        if ($record_obj == false) {
            $this->template('error_404');
            die();
        } else {
            $data = (array) $record_obj;
            return $data;        
        }
    }

    function _get_data_from_post() {
        $data['article_headline'] = post('article_headline', true);
        $data['article_body'] = post('article_body');
        $data['date_and_time'] = post('date_and_time', true);
        $data['published'] = post('published', true);        
        return $data;
    }

    function _init_picture_settings() { 
        $picture_settings['max_file_size'] = 2000;
        $picture_settings['max_width'] = 2600;
        $picture_settings['max_height'] = 2600;
        $picture_settings['resized_max_width'] = 760;
        $picture_settings['resized_max_height'] = 950;
        $picture_settings['destination'] = 'news_pics';
        $picture_settings['target_column_name'] = 'picture';
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

            $validation_str = 'allowed_types[gif,jpg,jpeg,png]|max_size['.$max_file_size.']|max_width['.$max_width.']|max_height['.$max_height.']';
            $this->validation_helper->set_rules('picture', 'item picture', $validation_str);

            $result = $this->validation_helper->run();

            if ($result == true) {

                $config['destination'] = $destination.'/'.$update_id;
                $config['max_width'] = $resized_max_width;
                $config['max_height'] = $resized_max_height;

                //upload the picture
                $this->upload_picture_alt($config);

                //update the database
                $data[$target_column_name] = $_FILES['picture']['name'];
                $this->model->update($update_id, $data);

                $flash_msg = 'The picture was successfully uploaded';
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

        $target_dir = APPPATH.'modules/news/assets/images/news_pics/'.$update_id;
        $this->_rrmdir($target_dir);

        $picture_settings = $this->_init_picture_settings();
        $target_column_name = $picture_settings['target_column_name'];
        $data[$target_column_name] = '';
        $this->model->update($update_id, $data);
        
        $flash_msg = 'The picture was successfully deleted';
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
}