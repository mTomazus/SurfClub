<?php
class Enquiries extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100);
    private $template_to_use = 'public';

    function index() {
        $data = $this->_get_data_from_post();
        $data['question'] = $this->_get_question();
        $data['options'] = $this->_get_possible_answers();
        $data['form_location'] = 'enquiries/submit'; 
        $data['view_module'] = 'enquiries';
        $data['view_file'] = 'contact_form';
        $this->template($this->template_to_use, $data);
    } 

    //change these to whatever take your fancy
    function _get_question() {
        $question = 'What is the capital of France?';
        return $question;
    }

    function _get_possible_answers() {
        $answer = post('answer', true);
        settype($answer, 'int');

        if ($answer == 0) {
            $answers[''] = 'Select...';
        }
        
        $answers[1] = 'Glasgow';
        $answers[2] = 'London';
        $answers[3] = 'New York';
        $answers[4] = 'Paris';
        return $answers;
    }

    function _get_correct_answer() {
        $correct_answer = 4;
        return $correct_answer;
    }

    function thankyou() {
        $data['view_module'] = 'enquiries';
        $data['view_file'] = 'thankyou';
        $this->template($this->template_to_use, $data);
    }   

    function manage() {

        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed('');

        if (segment(4) !== '') {
            $data['headline'] = 'Search Results';
            $searchphrase = trim($_GET['searchphrase']);
            $params['name'] = '%'.$searchphrase.'%';
            $params['email_address'] = '%'.$searchphrase.'%';
            $sql = 'select * from enquiries
            WHERE name LIKE :name
            OR email_address LIKE :email_address
            ORDER BY date_created desc';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Enquiries';
            $all_rows = $this->model->get('date_created desc');
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->_get_limit();
        $pagination_data['pagination_root'] = 'enquiries/manage';
        $pagination_data['record_name_plural'] = 'enquiries';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->_reduce_rows($all_rows);
        $data['selected_per_page'] = $this->_get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'enquiries';
        $data['view_file'] = 'manage';
        $this->template('admin', $data);
    }

    function show() {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();
        $update_id = segment(3);

        $this->_set_to_opened($update_id);

        if ((!is_numeric($update_id)) && ($update_id != '')) {
            redirect('enquiries/manage');
        }

        $data = $this->_get_data_from_db($update_id);
        $data['opened'] = ($data['opened'] == 1 ? 'yes' : 'no');
        $data['token'] = $token;

        if ($data == false) {
            redirect('enquiries/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['headline'] = 'Enquiry Information';
            $data['view_file'] = 'show';
            $this->template('admin', $data);
        }
    }

    function _set_to_opened($update_id) {
        $data['opened'] = 1;
        $this->model->update($update_id, $data, 'enquiries');
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
                $row->opened = ($row->opened == 1 ? 'yes' : 'no');
                $rows[] = $row;
            }
        }

        return $rows;
    }

    function submit() {

        $submit = post('submit', true);

        if ($submit == 'Submit') {

            $this->validation_helper->set_rules('name', 'Name', 'required|min_length[2]|max_length[255]');
            $this->validation_helper->set_rules('email_address', 'Email Address', 'required|min_length[5]|max_length[255]|valid_email_address|valid_email');
            $this->validation_helper->set_rules('message', 'Message', 'required|min_length[2]');
            $this->validation_helper->set_rules('answer', 'prove you are human answer', 'required|callback_answer_check');

            $result = $this->validation_helper->run();

            if ($result == true) {
                $data = $this->_get_data_from_post();
                $data['opened'] = ($data['opened'] == 1 ? 1 : 0);
                $data['date_created'] = time();
                unset($data['answer']);

                //insert the new record
                $update_id = $this->model->insert($data, 'enquiries');
                $finish_url = 'enquiries/thankyou';
                
                set_flashdata($flash_msg);
                redirect($finish_url);

            } else {
                //form submission error
                $this->index();
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
            $params['module'] = 'enquiries';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'enquiries');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('enquiries/manage');
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
        redirect('enquiries/manage');
    }

    function _get_data_from_db($update_id) {
        $record_obj = $this->model->get_where($update_id, 'enquiries');

        if ($record_obj == false) {
            $this->template('error_404');
            die();
        } else {
            $data = (array) $record_obj;
            return $data;        
        }
    }

    function _get_data_from_post() {
        $data['name'] = post('name', true);
        $data['email_address'] = post('email_address', true);
        $data['message'] = post('message', true);
        $data['opened'] = post('opened', true);   
        $data['answer'] = post('answer', true);     
        return $data;
    }

    function answer_check($str) {
        settype($str, 'int');

        $correct_answer = $this->_get_correct_answer();
        if ($str == $correct_answer) {
            return true;
        } else {
            $error_msg = 'You did not select the correct answer';
            return $error_msg;
        }
    }

}