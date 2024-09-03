<?php
    class Members extends Trongate {
        
        function index() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('members area');
            $data['view_file'] = 'members_home';
            $this->template('members_area', $data);
        }

        function _make_sure_allowed() {
            //Make sure the user is loged in as member
            $this->module('trongate_tokens');
            $token = $this->trongate_tokens->_attempt_get_valid_token(2);
       
            if($token === false) {
                redirect('members/login');
            } else {
                return $token;
            }
        }

        function account_created() {
            $data['view_file'] = 'account_created';
            $this->template('public', $data);
        }

        function create_account() {
            $data['username'] = post('username');
            $data['view_file'] = 'create_account';
            $this->template('public', $data);
        }

        function login() {
            $data['username'] = post('username');
            $data['view_file'] = 'member_login';
            $this->template('public', $data);
        }
        function logout() {
            $this->module('trongate_tokens');
            $this->trongate_tokens->_destroy();
            redirect('members/login');
        }

        function submit_create_account() {

            $this->validation_helper->set_rules('username', 'username', 'required|min_length[6]|max_length[55]|callback_username_unique');
            $this->validation_helper->set_rules('password', 'password', 'required|min_length[6]|max_length[55]');
            $this->validation_helper->set_rules('repeat_password', 'repeat password', 'required|matches[password]');

            $result = $this->validation_helper->run(); //returns true or false
        
            if($result === true) {
                // Create new member account.

                // Start by creating a new record on Trongate users.
                $trongate_user_data['code'] = make_rand_str(32);
                $trongate_user_data['user_level_id'] = 2; // member id.
                $trongate_user_id = $this->model->insert($trongate_user_data, 'trongate_users');

                // Now build up array of $data for the members record.
                $data['username'] = post('username', true);
                $password = post('password');
                $data['password'] = $this->hash_string($password);
                $data['trongate_user_id'] = $trongate_user_id;

                // Create new members ecord in dattabase.
                $this->model->insert($data, 'members');

                redirect('members/account_created');

            } else {
                $this->create_account();
            }
        }

        function submit_login() {
            $this->validation_helper->set_rules('username', 'username', 'required|callback_login_check');
            $this->validation_helper->set_rules('password', 'password', 'required');
        
            $result = $this->validation_helper->run(); // Returns true or false.
        
            if($result === false) {
                $this->login();
            } else {
                $username = post('username');
                $remember = (int) post('remember');
                $this->_in_you_go($username, $remember);
            }
        }

        function _in_you_go($username, $remember) {
            // Get trongate_user_id for this user.
            $member_obj = $this->model->get_one_where('username', $username, 'members');
            $trongate_user_id = $member_obj->trongate_user_id;

            // Create trongate token using token model.
            $this->module('trongate_tokens');
            $trongate_token_data['user_id'] = $trongate_user_id;

            if($remember === 1) {
                $trongate_token_data['set_cookie'] = true;
            }

            $this->trongate_tokens->_generate_token($trongate_token_data);

            // Send the user to private member's area.
            redirect('members');
        }

        private function hash_string(string $str): string {
            $hashed_string = password_hash($str, PASSWORD_BCRYPT, array(
                'cost' => 11
            ));
            return $hashed_string;
        }
    
        private function verify_hash(string $plain_text_str, string $hashed_string): bool {
            $result = password_verify($plain_text_str, $hashed_string);
            return $result; //TRUE or FALSE
        }

        function username_unique($username) {
            $member_obj = $this->model->get_one_where('username', $username, 'members');
            if($member_obj === false) {
                return true; // username is available
            } else {
                $error_msg = 'Username is not available!';
                return $error_msg;
            }
        }

        function login_check($username) {

            $error_msg = 'Your username and/or password was not correct!';

            // Make sure this username exist on members table.
            $member_obj = $this->model->get_one_where('username', $username, 'members');
            if($member_obj === false) {
                return $error_msg;
            } 
            // Check to see if password is valid
            $password = post('password');

            $stored_password = $member_obj->password;
            $is_password_valid = $this->verify_hash($password, $stored_password); // Return true or false

            if($is_password_valid === true) {
                return true;
            } else {
                return $error_msg;
            }

        }

    }