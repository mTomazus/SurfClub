<?php
    class Users extends Trongate {

        private const USER_TABLES = ['comp_users', 'comp_organizations', 'comp_judges'];
        private const ERROR_MSG = 'Your email and/or password was not correct!';
        private const MAX_LOGIN_ATTEMPTS = 5;
        private const LOCKOUT_DURATION = 900; // 15 minutes in seconds
        private const TOKEN_EXPIRY_REMEMBER = 2592000; // 30 days
        private const TOKEN_EXPIRY_SESSION = 3600; // 1 hour

        function index() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('participants area');
            $data['view_file'] = 'dashboard';
            $this->template('users_area', $data);
        }

        function _make_sure_allowed() {
            //Make sure the user is loged in as participant (user_level_id = 5)
            $this->module('trongate_tokens');
            $token = $this->trongate_tokens->_attempt_get_valid_token(5);
       
            if($token === false) {
                redirect('competitions-users/login');
            } else {
                return $token;
            }
        }

        function _get_user_info() {
            $this->module('trongate_tokens');
            $trongate_user_id = $this->trongate_tokens->_get_user_id();

            $sql = "SELECT cu.id, cu.name, cu.phone, cu.email, cup.dob, cup.gender, cup.club_name, cup.avatar
                    FROM comp_users AS cu
                    LEFT JOIN comp_users_profiles AS cup
                    ON cu.id = cup.user_id
                    WHERE cu.trongate_user_id = ?
                    LIMIT 1";
            $data = [$trongate_user_id];
            $user_info = $this->model->query_bind($sql, $data, 'array');
            $user_info[0]['user_age'] = $this->find_age_end($user_info[0]['dob'] ?? null);
            return $user_info;
        }

        function _get_user_comps() {
            $this->module('trongate_tokens');
            $trongate_user_id = $this->trongate_tokens->_get_user_id();

            $sql = "SELECT cn.id, cn.name, cp.user_id, cp.gender_age, cn.location, cn.year, cn.status, cn.entry_type, cp.id AS record_id, cp.status AS participation_status
                    FROM comp_users AS cu
                    LEFT JOIN comp_participants AS cp
                    ON cu.id = cp.user_id
                    LEFT JOIN comp_name AS cn
                    ON cp.comp_id = cn.id
                    WHERE cu.trongate_user_id = ?";
            $data = [$trongate_user_id];
            $user_comps = $this->model->query_bind($sql, $data, 'array');
            return $user_comps;
        }

        function login() {
            $data['email'] = post('email', true);
            $data['view_file'] = 'login';
            $this->template('public', $data);
        }

        function logout() {
            $this->module('trongate_tokens');
            $this->trongate_tokens->_destroy();
            redirect('competitions-users');
        }

        function profile_info() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('participants area');
            $this->view('profile_modal');
        }

        function submit_create_account() {

            $this->validation->set_rules('name', 'name', 'min_length[6]|max_length[55]');
            $this->validation->set_rules('email', 'email', 'required|min_length[6]|valid_email|callback_email_unique');
            $this->validation->set_rules('password', 'password', 'required|min_length[6]|max_length[55]');
            $this->validation->set_rules('repeat_password', 'repeat password', 'required|matches[password]');

            $result = $this->validation->run(); //returns true or false
        
            if($result === true) {
                // Create new participant account.

                // Start by creating a new record on Trongate users.
                $trongate_user_data['code'] = make_rand_str(32);
                $trongate_user_data['user_level_id'] = 5; // comp member id.
                $trongate_user_id = $this->model->insert($trongate_user_data, 'trongate_users');

                // Now build up array of $data for the comp_users record.
                $data['name'] = post('name', true);
                $data['email'] = post('email', true);
                $password = post('password');
                $data['password'] = $this->hash_string($password);
                $data['trongate_user_id'] = $trongate_user_id;
                $data['created_at'] = time();
                
                // Create new user record in database.
                $this->model->insert($data, 'comp_users');


                // Create a new token to auto login the user.
                $this->module('trongate_tokens');
                $token_data['trongate_user_id'] = $trongate_user_id;
                $this->trongate_tokens->_generate_token($token_data);

                redirect('competitions-users');

            } else {
                $this->login();
            }
        }

        function submit_update_profile() {
            $this->validation->set_rules('name', 'name', 'min_length[6]|max_length[55]');
            $this->validation->set_rules('email', 'email', 'min_length[6]|valid_email');
            $this->validation->set_rules('phone', 'phone', 'min_length[8]|max_length[15]');
            $this->validation->set_rules('dob', 'dob', 'min_length[8]|max_length[15]');
            $this->validation->set_rules('gender', 'gender', 'min_length[4]|max_length[6]');
            $this->validation->set_rules('club_name', 'club_name', 'min_length[8]|max_length[25]');
            
            $result = $this->validation->run(); //returns true or false
        
            if($result === true) {
                // Update participant account.
                $user_info = $this->_get_user_info();
                $id = $user_info->id;

                $data['gender'] = post('gender', true);
                $data['club_name'] = post('club_name', true);
                $data['dob'] = post('dob', true);

                $this->model->update_where('user_id', $id, $data, 'comp_users_profiles');
                exit();
                $email = post('email', true);
                if ($user_info->email === $email ) {
                    $data['name'] = post('name', true);
                    $data['email'] = $email;
                    $data['phone'] = post('phone', true);
                    $this->model->update($id, $data, 'comp_users');
                    set_flashdata('Profile updated successfully!');
                } else {
                    if ($this->email_unique($email) === true) {
                        $data['name'] = post('name', true);
                        $data['email'] = $email;
                        $data['phone'] = post('phone', true);
                        $this->model->update($id, $data, 'comp_users');
                        set_flashdata('Profile updated successfully!');
                    } else {
                        http_response_code(422);
                        set_flashdata('Email is not available!');
                    }
                }
            }
        }

        function email_unique($email) {
            $member_obj = $this->model->get_one_where('email', $email, 'comp_users');
            if($member_obj === false) {
                return true; // email is not in table, means available
            } else {
                $error_msg = 'email is not available!';
                return $error_msg;
            }
        }

        function submit_login() {

            $this->validation->set_rules('email', 'email', 'required|callback_login_check');
            $this->validation->set_rules('password', 'password', 'required');

            $result = $this->validation->run(); // true/false

            if ($result === false) {
                $this->login(); // re-render form with errors
                return;
            }

            $email = $this->normalize_email((string) post('email'));
            $remember = (int) post('remember');

            // We know credentials are valid, now load the user again to start session/token.
            [$user, $table] = $this->find_user_by_email($email);

            if (!$user) {
                // Very unlikely if validation passed, but handle gracefully
                $this->validation->set_error('A login error occurred. Please try again.');
                $this->login();
                return;
            }

            $this->_in_you_go($user, $table, $remember);
        }

        function _in_you_go($user_row, $source_table, int $remember = 0) {
            // Regenerate session id to prevent fixation
            if (function_exists('session_regenerate_id')) {
                session_regenerate_id(true);
            }

            // Build minimal session payload (adjust to your app’s session utils)

            $organizerTables = ['comp_organizations', 'comp_judges'];
            $role = in_array($source_table, $organizerTables, true) ? 'organizer' : 'participant';

            $_SESSION['auth'] = [
                'user_id' => (int) ($user_row->id ?? 0),
                'trongate_user_id' => (int) ($user_row->trongate_user_id ?? 0),
                'role' => $role,
                'email' => $this->normalize_email($user_row->email ?? ''),
                'table' => $source_table,
                'logged_in_at' => time(),
            ];

            // Create last login record
            $update_data['last_login'] = time();
            $this->model->update($user_row->id, $update_data, $source_table);

            // Create Trongate token
            $this->module('trongate_tokens');
            $token_data = [
                'user_id' => (int) ($user_row->trongate_user_id ?? 0),
            ];
            if ($remember === 1) {
                $token_data['set_cookie'] = true; // Trongate will set a cookie
                // Optionally: $token_data['expiry_date'] = ... if your tokens module supports it
            }
            $this->trongate_tokens->_generate_token($token_data);

            // Role-based redirect
            if ($role === 'organizer') {
                redirect('competitions'); // organizer dashboard
            } else {
                redirect('competitions-users'); // participants dashboard
            }
        }

        // --- Helpers --------------------------------------------------------------

        private function normalize_email(string $email): string {
            return strtolower(trim($email));
        }

        private function find_user_by_email(string $email, array $tables = self::USER_TABLES): array {

            foreach ($tables as $table) {
                $row = $this->model->get_one_where('email', $email, $table);
                if ($row !== false) {
                    return [$row, $table];
                }
            }
            return [false, null];
        }

        private function find_age_end($birthdate) {
            // $birthdate should be in format YYYY-MM-DD
            $birth = new DateTime($birthdate);

            // Get the last day of the current year
            $endOfYear = new DateTime(date('Y') . '-12-31');

            // Calculate the difference
            $age = $birth->diff($endOfYear)->y;

            return $age;
        }

        // Optional: store a precomputed dummy hash in config for timing equalization
        private function dummy_hash(): string {
            if (defined('DUMMY_HASH')) {
                return DUMMY_HASH;
            }
            // Fallback dummy bcrypt (format only; replace with a real hash from your hasher)
            return '$2y$11$abcdefghijklmnopqrstuvABCDE1234567890wxyzABCDE12';
        }

        private function hash_string(string $str): string {
            return password_hash($str, PASSWORD_BCRYPT, ['cost' => 11]);
        }

        private function verify_hash(string $plain_text_str, string $hashed_string): bool {
            return password_verify($plain_text_str, $hashed_string);
        }

        // --- Validation callback --------------------------------------------------

        function login_check($email) {
            $error_msg = self::ERROR_MSG;
            $email = $this->normalize_email((string) $email);
            $password = (string) post('password');

            // Try to find user in either table
            [$user, $table] = $this->find_user_by_email($email);
            if ($user === false || $table === null) {
                return $error_msg;
            }

            if ($user && isset($user->lockout_time) && (int)$user->lockout_time > time()) {
                $error_msg = 'Too many login attempts. Please try again later.';
                return $error_msg;
            }

            // Use real hash if found, otherwise dummy to match timing
            $stored_hash = $user ? $user->password : $this->dummy_hash();

            $is_valid = $this->verify_hash($password, $stored_hash);

            if (!$user || !$is_valid) {
                // soft rate limit here
                if ($user) {
                    $num_logins = (int) $user->num_logins + 1;
                    $update_data['num_logins'] = $num_logins;

                    if ($num_logins >= self::MAX_LOGIN_ATTEMPTS) {
                        $update_data['lockout_time'] = time() + self::LOCKOUT_DURATION;
                        $update_data['num_logins'] = 0; // reset counter after lockout
                        $error_msg = 'Too many login attempts. Please try again later.';
                    }
                    $data['num_logins'] = (int) $user->num_logins + 1;
                    $this->model->update($user->id, $update_data, $table);
                }

                return $error_msg;
            }
            $update_data['num_logins'] = 0; // reset counter after lockout
            $this->model->update($user->id, $update_data, $table);

            return true; // validation passes
        }

        // --- Modals  ---------------------------------------

        function organizer() {
            $org_id = (int)segment(3);
            $organizer = $this->model->get_where($org_id, 'comp_organizations');
            $data['organizer'] = $organizer;
            $this->view('organizer_modal', $data);
        }

        function withdraw() {
            $data['record_id'] = (int)segment(3);
            $this->view('withdraw_modal', $data);
        }

        function competition() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('participants area');
            $comp_id = (int)segment(3);
            $user = $this->_get_user_info();
            $data['user'] = $user;
            $data['user_age'] = $this->find_age_end($user[0]['dob']);

            $competition = $this->model->get_where($comp_id, 'comp_name');
            $organizer = $this->model->get_where($competition->organizer_id, 'comp_organizations');
              
            $sql = "SELECT * 
                    FROM comp_competition_divisions
                    LEFT JOIN comp_divisions
                    ON comp_competition_divisions.division_id = comp_divisions.id 
                    WHERE competition_id = ?";

            // Unnamed parameters to bind to the query.
            $data = [$comp_id];

            // Execute the query using the unnamed parameters.
            $rows = $this->model->query_bind($sql, $data, 'array');

            $data['divisions'] = $rows;
            $data['competition'] = $competition;
            $data['organizer'] = $organizer;    
            $this->view('competition_modal', $data);
        }
        
        // --- Actions ---------------------------------------

        function confirm_withdraw() {
            $record_id = (int)segment(3);
            $user = $this->_get_user_info();
            $user_id = $user[0]['id'];
            if (from_trongate_mx()) {
                $participant = $this->model->get_one_where('id', $record_id, 'comp_participants');
                if ($user_id != $participant->user_id) {
                    set_flashdata('Request is NOT allowed');
                    redirect('competitions-users');
                } else {
                    $is_deleted = $this->model->delete($record_id, 'comp_participants');
                    set_flashdata('You have successfully withdrawn from the competition.');
                    redirect('competitions-users');
                }
            } else {
                set_flashdata('You are not registered for this competition.');
                redirect('competitions-users');
            };
        }

        function join() {
            
            $data['comp_id'] = segment(3, 'int');
            $user = $this->_get_user_info();
            $data['user_id'] = $user[0]['id'];

            $this->validation->set_rules('division', 'division', 'min_length[6]|max_length[15]');
            $result = $this->validation->run(); //returns true or false
        
            if($result === true) {

                $data['gender_age'] = post('division', true);

                //insert the new record
                $this->model->insert($data, 'comp_participants');
                $flash_msg = 'The record was successfully created';
                set_flashdata($flash_msg);
            }

        }

        public function search() {
            // get user id
            $user = $this->_get_user_info();
            $user_id = $user[0]['id'];
            // q from query string: /competitions/search?q=...
            $q = trim($_GET['q']);
            if ($q === '' || mb_strlen($q) < 2) {
                header('Content-Type: application/json');
                echo json_encode([]); exit;
            }

            // Build bind once; use CONCAT in SQL so we don't have to inject % in PHP
            $bind1 = ['q' => $q, 'user_id' => $user_id];
            $bind2 = ['q' => $q];

            // --- Competitions (comp_name) ---
            // Assumptions:
            //   comp_name: id, name, organizer_id, status (optional)
            //   comp_organizers: id, name
            // If you don't have c.status, remove the column from SELECT.
            $sql1 = "SELECT
                        c.id,
                        c.name AS title,
                        c.year,
                        c.entry_type,
                        o.organization AS organiser,                         
                        LOWER(COALESCE(c.status, 'unknown')) AS status,
                        'competition' AS type,
                        o.country
                    FROM comp_name c
                    LEFT JOIN comp_organizations o ON o.id = c.organizer_id
                    WHERE COALESCE(c.status,'') <> 'finished'
                    AND NOT EXISTS (
                        SELECT 1
                        FROM comp_participants p
                        WHERE p.comp_id = c.id
                        AND p.user_id = :user_id
                    )
                    AND (
                        c.name LIKE CONCAT('%', :q, '%')
                        OR o.organization LIKE CONCAT('%', :q, '%')
                    )
                    ORDER BY c.name
                    LIMIT 20";
            $competitions = $this->model->query_bind($sql1, $bind1, 'array');

            // --- Organisers (comp_organizers) ---
            // Assumptions:
            //   comp_organizers: id, name, city (optional), status (optional)
            $sql2 = "SELECT
                        o.id,
                        o.organization AS title,
                        'active' AS status,
                        'organiser' AS type,
                        o.country
                    FROM comp_organizations o
                    WHERE o.organization LIKE CONCAT('%', :q, '%')
                    ORDER BY o.organization
                    LIMIT 20";
            $organisers = $this->model->query_bind($sql2, $bind2, 'array');

            $out = array_merge($competitions, $organisers);

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($out);
            exit;
        }

    }