<?php
class Welcome extends Trongate {

	/**
	 * Renders the (default) homepage for public access.
	 *
	 * @return void
	 */
	public function welcome(): void {
		$data['view_module'] = 'welcome';
		$data['view_file'] = 'welcome';
		$this->template('public', $data);
	}
	public function politika(): void {
		$data['view_module'] = 'welcome';
		$data['view_file'] = 'privatumo_politika';
		$this->template('public', $data);
	}
	public function taisykles(): void {
		$data['view_module'] = 'welcome';
		$data['view_file'] = 'puslapio_taisykles';
		$this->template('public', $data);
	}
	public function grazinimas(): void {
		$data['view_module'] = 'welcome';
		$data['view_file'] = 'prekiu_grazinimas';
		$this->template('public', $data);
	}
	public function kontaktai(): void {
		$data['view_module'] = 'welcome';
		$data['view_file'] = 'kontaktai';
		$this->template('public', $data);
	}
	public function home(): void {
		$data['view_module'] = 'welcome';
		$data['view_file'] = 'home';
		$this->template('public', $data);
	}
	public function index(): void {
		$data['view_module'] = 'welcome';
		$data['view_file'] = 'stovykla';
		$this->template('public', $data);
	}
	public function varzybos(): void {
		$data['view_module'] = 'welcome';
		$data['view_file'] = 'varzybos';
		$this->template('public', $data);
	}
	public function parama(): void {
		$data['view_module'] = 'welcome';
		$data['view_file'] = 'parama';
		$this->template('public', $data);
	}
	public function stovykla(): void {
		$data['view_module'] = 'welcome';
		$data['view_file'] = 'stovykla';
		$this->template('public', $data);
	}
	public function nuoma(): void {
		$data['view_module'] = 'welcome';
		$data['view_file'] = 'nuoma';
		$this->template('public', $data);
	}
    public function stovykla_modal() {
        echo '<p>stovyklos registracijos modalas</p>';
    }
	public function pamokos(): void {
		$path = BASE_URL."public/images/pamokos/"; // Path for url src
		$dir = APPPATH."public/images/pamokos/"; // Folder containing the images
		$images = scandir($dir); // Read all files in the directory, includes . and  ..
		unset($images[0], $images[1]); // Remove . and .. from array 

		$data['path'] = $path;
		$data['images'] = $images;
		$data['view_module'] = 'welcome';
		$data['view_file'] = 'pamokos';
		$this->template('public', $data);
	}

    function _init_picture_settings() { 
        $picture_settings['max_file_size'] = 2000;
        $picture_settings['max_width'] = 1200;
        $picture_settings['max_height'] = 1200;
        $picture_settings['resized_max_width'] = 450;
        $picture_settings['resized_max_height'] = 450;
        $picture_settings['destination'] = 'welcome_pics';
        $picture_settings['target_column_name'] = 'picture';
        $picture_settings['thumbnail_dir'] = 'welcome_pics_thumbnails';
        $picture_settings['thumbnail_max_width'] = 120;
        $picture_settings['thumbnail_max_height'] = 120;
        $picture_settings['upload_to_module'] = true;
        $picture_settings['make_rand_name'] = false;
        return $picture_settings;
    }

    function _make_sure_got_destination_folders($update_id, $picture_settings) {

        $destination = $picture_settings['destination'];

        if ($picture_settings['upload_to_module'] == true) {
            $target_dir = APPPATH.'modules/'.segment(1).'/assets/'.$destination.'/'.$update_id;
        } else {
            $target_dir = APPPATH.'public/'.$destination.'/'.$update_id;
        }

        if (!file_exists($target_dir)) {
            //generate the image folder
            mkdir($target_dir, 0777, true);
        }

        //attempt to create thumbnail directory
        if (strlen($picture_settings['thumbnail_dir'])>0) {
            $ditch = $destination.'/'.$update_id;
            $replace = $picture_settings['thumbnail_dir'].'/'.$update_id;
            $thumbnail_dir = str_replace($ditch, $replace, $target_dir);
            if (!file_exists($thumbnail_dir)) {
                //generate the image folder
                mkdir($thumbnail_dir, 0777, true);
            }
        }

    }

    function submit_upload_picture($update_id) {

        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if ($_FILES['picture']['name'] == '') {
            redirect($_SERVER['HTTP_REFERER']);
        }

        $picture_settings = $this->_init_picture_settings();
        extract($picture_settings);

        $validation_str = 'allowed_types[gif,jpg,jpeg,png]|max_size['.$max_file_size.']|max_width['.$max_width.']|max_height['.$max_height.']';
        $this->validation->set_rules('picture', 'item picture', $validation_str);

        $result = $this->validation->run();

        if ($result == true) {

            $config['destination'] = $destination.'/'.$update_id;
            $config['max_width'] = $resized_max_width;
            $config['max_height'] = $resized_max_height;

            if ($thumbnail_dir !== '') {
                $config['thumbnail_dir'] = $thumbnail_dir.'/'.$update_id;
                $config['thumbnail_max_width'] = $thumbnail_max_width;
                $config['thumbnail_max_height'] = $thumbnail_max_height;
            }

            //upload the picture
            $config['upload_to_module'] = (!isset($picture_settings['upload_to_module']) ? false : $picture_settings['upload_to_module']);
            $config['make_rand_name'] = $picture_settings['make_rand_name'] ?? false;

            $file_info = $this->upload_picture($config);

            //update the database with the name of the uploaded file
            $data[$target_column_name] = $file_info['file_name'];
            $this->model->update($update_id, $data);

            $flash_msg = 'The picture was successfully uploaded';
            set_flashdata($flash_msg);
            redirect($_SERVER['HTTP_REFERER']);

        } else {
            redirect($_SERVER['HTTP_REFERER']);
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

        $picture_settings = $this->_init_picture_settings();
        $target_column_name = $picture_settings['target_column_name'];
        $picture_name = $result->$target_column_name;

        if ($picture_settings['upload_to_module'] == true) {
            $picture_path = APPPATH.'modules/'.segment(1).'/assets/'.$picture_settings['destination'].'/'.$update_id.'/'.$picture_name;
        } else {
            $picture_path = APPPATH.'public/'.$picture_settings['destination'].'/'.$update_id.'/'.$picture_name;
        }

        $picture_path = str_replace('\\', '/', $picture_path);

        if (file_exists($picture_path)) {
            unlink($picture_path);
        }

        if (isset($picture_settings['thumbnail_dir'])) {
            $ditch = $picture_settings['destination'].'/'.$update_id;
            $replace = $picture_settings['thumbnail_dir'].'/'.$update_id;
            $thumbnail_path = str_replace($ditch, $replace, $picture_path);

            if (file_exists($thumbnail_path)) {
                unlink($thumbnail_path);
            }
        }

        $data[$target_column_name] = '';
        $this->model->update($update_id, $data);
        
        $flash_msg = 'The picture was successfully deleted';
        set_flashdata($flash_msg);
        redirect($_SERVER['HTTP_REFERER']);
    }
}