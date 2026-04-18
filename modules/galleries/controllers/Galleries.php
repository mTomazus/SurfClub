<?php
class Galleries extends Trongate {

    private $default_limit = 10;
    private $per_page_options = array(10, 20, 50, 100);
    
    public function index(): void {
        $sql = 'SELECT * FROM galleries ORDER BY year DESC, pamaina ASC';
        $all_galleries = $this->model->query($sql, 'object');
        $data['galleries_by_year'] = [];
        foreach ($all_galleries as $gallery) {
            $data['galleries_by_year'][$gallery->year][] = $gallery;
        }
        $data['view_file'] = 'public_index';
        $this->template('public', $data);
    }

    public function pamaina(): void {
            $this->module('trongate_filezone');
            $year = (int) segment(3);
            $camp = (int) segment(4);

            $sql = 'SELECT * FROM galleries WHERE year = ? AND pamaina = ? LIMIT 1';
            $result = $this->model->query_bind($sql, [$year, $camp], 'object');
            $pamaina = $result[0] ?? false;

            if ($pamaina === false) redirect('error_404');

            $update_id = $pamaina->id ?? 0;

            $filezone_settings = $this->_init_filezone_settings();

            $pic_dir = APPPATH . 'modules/galleries/assets/' . $filezone_settings['destination'] . '/' . $update_id;

            $pictures = scandir($pic_dir);

            foreach ($pictures as $key => $value) {
                if (($value !== '.') && ($value !== '..') && ($value !== '.DS_Store') && ($value !== 'thumbnails')) {
                    $pics[] = $value;
                }
            }
            // If no pictures are found, redirect to the gallery index page
            if (!isset($pics)) {
                $this->index();
            }
            $pagination_data["total_rows"] = count($pics);
            $pagination_data["page_num_segment"] = 5;
            $pagination_data["limit"] = $this->get_limit();
            $pagination_data["pagination_root"] = "galleries/pamaina/$year/$camp";
            $pagination_data["record_name_plural"] = "pictures";
            $pagination_data["include_showing_statement"] = true;
            $data["pagination_data"] = $pagination_data;
            $data["pictures"] = $this->reduce_rows($pics, 5);

            $data['pamaina'] = $pamaina->pamaina ?? 'Gallery';
            $data['year'] = $year;
            $data['update_id'] = $update_id;

            $data['view_file'] = 'gallery_index';
            $this->template('public', $data);
    }

    public function make_thumbnails(): void {
        $this->module('trongate_filezone');
        $camp = (int) segment(3);
        $pamaina = $this->model->get_one_where('pamaina', $camp, 'galleries');
        $update_id = $pamaina->id ?? 0;

        $filezone_settings = $this->_init_filezone_settings();
        $pic_dir = APPPATH . 'modules/galleries/assets/' . $filezone_settings['destination'] . '/' . $update_id;

        $image_path = $pic_dir . '/';
        $thumb_path = $pic_dir . '/thumbnails/';
        
        $pictures = scandir($pic_dir);
        
        if (!is_dir($pic_dir . '/thumbnails')) {
            if (!mkdir($thumb_path, 0777, true)) {
                die('Failed to create directory...');
            }
        }

        foreach ($pictures as $value) {
            if (($value !== '.') && ($value !== '..') && ($value !== '.DS_Store') && ($value !== 'thumbnails')) {
                $save_path = $thumb_path . $value;
                $load_path = $image_path . $value;
                $image = new Image();
                try {
                    $image->load($load_path);
                    $image->resize_to_width(350);
                    $image->save($save_path, 80); // 80% quality
                    $image->destroy();
                } catch (Exception $e) {
                    echo "Error processing image $value: " . $e->getMessage() . "\n";
                }
            }
        }
        echo "Thumbnails created successfully.";
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
            $data['headline'] = 'Update Gallery Record';
            $data['cancel_url'] = BASE_URL.'galleries/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Gallery Record';
            $data['cancel_url'] = BASE_URL.'galleries/manage';
        }

        $data['form_location'] = BASE_URL.'galleries/submit/'.$update_id;
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
            $params['pamaina'] = '%'.$searchphrase.'%';
            $sql = 'select * from galleries
            WHERE pamaina LIKE :pamaina
            ORDER BY id desc';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Galleries';
            $all_rows = $this->model->get('id desc');
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->get_limit();
        $pagination_data['pagination_root'] = 'galleries/manage';
        $pagination_data['record_name_plural'] = 'galleries';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->reduce_rows($all_rows);
        $data['selected_per_page'] = $this->get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'galleries';
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
            redirect('galleries/manage');
        }

        $data = $this->get_data_from_db($update_id);
        $data['token'] = $token;

        if ($data === false) {
            redirect('galleries/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['headline'] = 'Gallery Information';
            $data['filezone_settings'] = $this->_init_filezone_settings();
            $data['view_file'] = 'show';
            $this->template('admin', $data);
        }
    }

    function _init_filezone_settings() {
        $data['targetModule'] = 'galleries';
        $data['destination'] = 'galleries_pictures';
        $data['max_file_size'] = 1200;
        $data['max_width'] = 2500;
        $data['max_height'] = 1400;
        $data['thumbnail_dir'] = 'thumbnails';
        $data['thumbnail_max_width'] = 320;
        $data['thumbnail_max_height'] = 320;
        $data['upload_to_module'] = true;
        return $data;
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

            $this->validation->set_rules('year', 'year', 'required|integer');
            $this->validation->set_rules('pamaina', 'pamaina', 'required|integer');

            $result = $this->validation->run();

            if ($result === true) {

                $update_id = (int) segment(3);
                $data = $this->get_data_from_post();
                
                if ($update_id>0) {
                    //update an existing record
                    $this->model->update($update_id, $data, 'galleries');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    //insert the new record
                    $update_id = $this->model->insert($data, 'galleries');
                    $flash_msg = 'The record was successfully created';
                }

                set_flashdata($flash_msg);
                redirect('galleries/show/'.$update_id);

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
            $params['module'] = 'galleries';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'galleries');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('galleries/manage');
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
        redirect('galleries/manage');
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
    private function reduce_rows(array $all_rows, int $offset_seg = 4): array {
        $rows = [];
        $start_index = $this->get_offset($offset_seg);
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
     * // pakeiciau is segment(3) i segment(4)
     * @return int Offset for pagination.
     */
    private function get_offset(int $seg = 4): int {
        $page_num = (int) segment($seg);

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
        $record_obj = $this->model->get_where($update_id, 'galleries');

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
        $data['year'] = (int) post('year', true);
        $data['pamaina'] = (int) post('pamaina', true);
        return $data;
    }

}