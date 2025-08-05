<?php
    class Competitions extends Trongate {
        
        function index() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            $judge = $this->_get_judge_info();
            $data['user'] = $judge;

            if(isset($judge)) {
                $data['view_file'] = 'judge_dash';
                $this->template('judges_area', $data);
            } else {
              $this->login();  
            }
        }

        function success() {
            $this->view('success');
        }

        public function heat_time() {
            date_default_timezone_set('Europe/Vilnius');

            $heat = $this->model->get_one_where('status', 'running', 'comp_heats');
            $future_time = new DateTime($heat->end_time); // Convert string to DateTime
            $current_time = new DateTime(); // Get current time

            if ($future_time > $current_time) {
                $interval = $current_time->diff($future_time);
            } else {
                $interval = $future_time->diff($current_time);
            }
            echo $interval;
            echo $interval->format('%i:%s');  // Outputs MM:SS
        }

        function current_time() {
            date_default_timezone_set('Europe/Vilnius');
            $time = date('h:i:s', time()) ;
            echo $time;
        }

//---------------------------------------------------------------
//----------------------- JUDGING COMP --------------------------
        
        function judge_scores() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            // Get judge info from comp_judges table
            $judge_info = $this->_get_judge_info();
            $data['judge'] = $judge_info;

            // Fetch currently running heat
            $heat = $this->_get_running_heat();
            
            if (!$heat) {
                date_default_timezone_set('Europe/Vilnius');
                $current_time = date("Y-m-d H:i:s");
                $data['error'] = "No heat has your scores yet!";
                $data['heat'] = $this->model->get_where_custom('status', 'scheduled', '=', 'start_time ASC', 'comp_heats', 1);
                $this->view('scores_error', $data);
                die();
            }

            $data['heat_id'] = $heat->id;  
            $this->view('judge_scores', $data);
        }

        function score_heat() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');
            
            date_default_timezone_set('Europe/Vilnius');

            $update_status = $this->update_heat_status();

            // Get judge info from comp_judges table
            $judge_info = $this->_get_judge_info();
            $judge_id = (int)$judge_info->id;
            $data['user'] = $judge_info;

            // Fetch currently running heat
            $heat = $this->_get_running_heat();

            if (!isset($heat)) {
                $data['error'] = "Heat have not started yet!";
                $data['heat'] = $this->model->get_where_custom('status', 'scheduled', '=', 'start_time ASC', 'comp_heats', 1);
                $data['view_file'] = 'live_error';
                $this->template('judges_area', $data);
                exit();
            }

            $heat_id = $heat->id;
            $data['heat_id'] = $heat_id;

            // Fetch all participants in the heat
            $sql = "SELECT participant_id FROM comp_heat_participants WHERE heat_id = ?";
            $participants = $this->model->query_bind($sql, [$heat_id], 'array');

            // Assign next wave numbers for each participant
            $wave_numbers = [];
            foreach ($participants as $p) {
                $participant_id = $p['participant_id'];
                
                // Get next wave number for the participant
                $next_wave_number = $this->_get_next_wave($heat_id, $participant_id, $judge_id);
                
                // Store the next wave number for each participant
                $wave_numbers[$participant_id] = $next_wave_number;
            }

            // Include the wave numbers array in the data
            $data['wave_numbers'] = $wave_numbers;

            // // Include the heat info array in the data
            $data['heat'] = $this->model->get_where($heat_id, 'comp_heats');

            $data['view_file'] = 'score_heat';
            $this->template('judges_area', $data);
        }

        function score_submit() {

            $heat_id = post('heat_id', true);
            $judge_id = post('judge_id', true);
            $jersey_color = post('jersey_color', true);
            $score = post('score', true); // Ensure score is a valid value (1.0 to 10.0, etc.)

            // Fetch participant based on jersey color
            $participant_id = $this->_get_participantId_By_JerseyColor($jersey_color, $heat_id);  

            // Fetch next available wave number
            $next_wave = $this->_get_next_wave($heat_id, $participant_id, $judge_id);

            // Prepare data to insert into comp_judge_scores
            $data = [
                'heat_id' => $heat_id,
                'judge_id' => $judge_id,
                'participant_id' => $participant_id,
                'score' => $score,
                'wave_number' => $next_wave
            ];

            // Insert the score into comp_judge_scores table
            $update_id = $this->model->insert($data, 'comp_judge_scores');
            $flash_msg = 'The score ' . $score . ' was added for wave no ' . $next_wave;

            // After submit to database NextWave becomes WaveNumber
            $wave_number = $next_wave;

            $this->_calculate_and_save_average($heat_id, $wave_number, $participant_id);
            set_flashdata($flash_msg);

        }

        function _calculate_and_save_average($heat_id, $wave_number, $participant_id) {
            // Fetch all scores for the wave and participant so far
            $sql = "SELECT score FROM comp_judge_scores 
                    WHERE heat_id = ? AND wave_number = ? AND participant_id = ?";
            $data = [$heat_id, $wave_number, $participant_id];
            $scores = $this->model->query_bind($sql, $data, 'object');

            // Calculate the new average
            $total_score = 0;
            $total_judges = count($scores);

            foreach ($scores as $score) {
                $total_score += $score->score;
            }

            // Avoid division by zero
            $average_score = $total_judges > 0 ? round($total_score / $total_judges, 2) : 0;

            $data_avg = [
                'heat_id' => $heat_id,
                'wave_number' => $wave_number,
                'participant_id' => $participant_id,
                'avg_score' => $average_score
            ];

            // Check if the average score already exists for this wave and participant
            $sql_existing = "SELECT id FROM comp_wave_averages WHERE heat_id = ? AND wave_number = ? AND participant_id = ?";
            $existing_data = [$heat_id, $wave_number, $participant_id];
            $existing_id = $this->model->query_bind($sql_existing, $existing_data, 'array');

            // Insert or update the average score in the `comp_wave_averages` table
            if ($existing_id) {
                $avg_id = $existing_id[0]['id'];
                $this->model->update($avg_id, $data_avg, 'comp_wave_averages');
            } else {
                $this->model->insert($data_avg, 'comp_wave_averages');
            }
        }        

        function _get_running_heat() {
            date_default_timezone_set('Europe/Vilnius');
            $current_time = date("Y-m-d H:i:s");

            // Check if there's an already running heat
            $running_heat = $this->model->get_where_custom('status', 'running', '=', 'id DESC', 'comp_heats', 1);

            if (isset($running_heat)) {
                // No running heat, find the next scheduled heat that should start
                $sql = "SELECT * FROM comp_heats WHERE status = 'scheduled' AND start_time <= ? ORDER BY start_time ASC LIMIT 1";
                $scheduled_heat = $this->model->query_bind($sql, [$current_time], 'array');

                if ($scheduled_heat) {
                    $heat_id = $scheduled_heat[0]['id'];

                    // Update heat status to "running"
                    $update_data = ['status' => 'running'];
                    $this->model->update($heat_id, $update_data, 'comp_heats');

                    return $scheduled_heat[0];
                }
            }
            
            return $running_heat ? $running_heat[0] : null;
        }

        function _get_next_wave($heat_id, $participant_id, $judge_id) {

            // Get the next wave number for this participant in the heat
            $sql = "SELECT COALESCE(MAX(wave_number), 0) + 1 AS next_wave FROM comp_judge_scores WHERE heat_id = ? AND participant_id = ? AND judge_id = ?";
            $result = $this->model->query_bind($sql, [$heat_id, $participant_id, $judge_id], 'object');
            
            return $result ? (int)$result[0]->next_wave : 1;
        }

        function _get_participantId_By_JerseyColor($jersey_color, $heat_id) {

            $sql = "SELECT participant_id FROM comp_heat_participants 
                    WHERE heat_id = ? AND jersey_color = ? LIMIT 1";
            $participant = $this->model->query_bind($sql, [$heat_id, $jersey_color], 'array');
            $participant_id = $participant[0]["participant_id"];

            return $participant_id ? (int)$participant_id : null;
        }

        function update_heat_status() {
            date_default_timezone_set('Europe/Vilnius');
            $current_time = date("Y-m-d H:i");

            // Step 1: Find the heat that is currently "Running" and has ended
            $sql = "SELECT * FROM comp_heats WHERE status = 'running' AND end_time <= ?";
            $running_heat = $this->model->query_bind($sql, [$current_time], 'array');

            if (!empty($running_heat)) {

                $heat_id = $running_heat[0]['id'];

                // Step 2: Introduce a delay (e.g., 2 minutes) before marking as finished
                $delay_time = date("Y-m-d H:i", strtotime("+2 minutes", strtotime($running_heat[0]['end_time'])));

                if ($current_time >= $delay_time) {
 
                    // Update heat results and put status to "Finished"
                    $this->module('competitions-heats');
                    $this->heats->_process_heat_results($heat_id);

                    $data['status'] = 'finished';
                    $this->model->update($heat_id, $data, 'comp_heats');

                    // Step 3: Find the next scheduled heat and update its status to "Running"
                    $sql_next = "SELECT * FROM comp_heats WHERE status = 'scheduled' AND start_time <= ? ORDER BY start_time ASC LIMIT 1";
                    $next_heat = $this->model->query_bind($sql_next, [$current_time], 'array');

                    if (!empty($next_heat)) {
                        $next_heat_id = $next_heat[0]['id'];
                        $next_data['status'] = 'running';
                        $this->model->update($next_heat_id, $next_data, 'comp_heats');
                    }

                } 
            }
        }

//---------------------------------------------------------------
//----------------------- PARTICIPANTS --------------------------

        function create_participant() {
            $comp = $this->model->get_one_where('status', 'open', 'comp_name');
            $comp_id = $comp->id ?? 0;
            $data['rows'] = $comp;
            $sql = "SELECT 
                    cd.id AS id,
                    cd.name AS name
                    FROM comp_competition_divisions ccd
                    JOIN comp_divisions cd ON ccd.division_id = cd.id
                    WHERE ccd.competition_id = $comp_id";
            $data['divisions'] = $this->model->query($sql, 'object');
            $data['view_file'] = 'create_participant';
            $this->template('public', $data);
        }

        function edit_participant() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            $record_id = (int) segment(3);
            
            $record_obj = $this->model->get_where($record_id, 'comp_participants');
            $comp_id = $record_obj->comp_id ?? 0;
            $competition = $this->model->get_one_where('id', $comp_id, 'comp_name');
            $division = $this->model->get_one_where('name', $record_obj->gender_age, 'comp_divisions');
            
            $data = (array)$record_obj;

            $sql = "SELECT 
                    cd.id AS id,
                    cd.name AS name
                    FROM comp_competition_divisions ccd
                    JOIN comp_divisions cd ON ccd.division_id = cd.id
                    WHERE ccd.competition_id = $comp_id";
            $data['divisions'] = $this->model->query($sql, 'object');

            $data['division_id'] = $division->id ?? 0;

            $data['row'] = $competition;

            $this->view('edit_participant', $data);
        }

        function submit_delete_participant() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            $record_id = (int)segment(3);

            $this->model->delete($record_id, 'comp_participants');
        }

        function show_participants() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');
        
            $show_only = $this->get_show_only(); // from segment(3)
            $comp_id = segment(4); // from segment(4), can be empty

            // Build filter array
            $where = [];
            $conditions = [];
            $sql = "SELECT p.*, c.name, c.year FROM comp_participants p
                    JOIN comp_name c ON p.comp_id = c.id
                    WHERE c.status = 'open'";

            if ($show_only != '') {
                $where['gender_age'] = $show_only;
                $conditions[] = 'p.gender_age = :gender_age';
            }
            if ($comp_id != '') {
                $where['comp_id'] = $comp_id;
                $conditions[] = 'p.comp_id = :comp_id';
            }

            if (!empty($conditions)) {
                $sql .= " AND " . implode(' AND ', $conditions);
            }

            $data['rows'] = $this->model->query_bind($sql, $where, 'object');
            $data['view_file'] = 'show_participants';
            $this->template('judges_area', $data);
        }

        function submit_create_participant() {

            $this->validation->set_rules('first_name', 'First Name', 'required|min_length[4]|max_length[55]');
            $this->validation->set_rules('last_name', 'Last Name', 'required|min_length[4]|max_length[55]');
            $this->validation->set_rules('email', 'Email', 'required|valid_email');
            $this->validation->set_rules('comp_id', 'comp_id', 'required|min_length[1]|integer');

            $result = $this->validation->run(); //returns true or false
            
            $update_id = segment(3, 'int');
        
            if($result === true) {
    
                // Now build up array of $data for the comp_participants record.
                $data['first_name'] = post('first_name', true);
                $data['last_name'] = post('last_name', true);
                $data['email'] = post('email', true);
                $data['comp_id'] = post('comp_id', true);

                $division_id = post('division_id', true);
                $gender_age = $this->model->get_one_where('id', $division_id, 'comp_divisions');
                $data['gender_age'] = $gender_age->name ?? '';

                if ($update_id>0) {
                    //update an existing record
                    $this->model->update($update_id, $data, 'comp_participants');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    //insert the new record
                    $update_id = $this->model->insert($data, 'comp_participants');
                    $flash_msg = 'The record was successfully created';
                }

                echo '<p style="color:white;background:green;text-align: center;padding: 0.5rem;">Participant successfully registered!</p>';

            } else {

                echo '<p style="color: black;background: orange;text-align: center;padding: 0.5rem;">Form fields have to be filled!</p>';
            
            }
        }

        private function get_comp_only() {
            $comp_only = ''; // Always initialize
            $comp_only_val = segment(4);
            
            if ($comp_only_val !== '') {
                $created_comp = $this->get_created_comp();
                foreach ($created_comp as $row) {
                    if ($comp_only_val == $row->id) {
                        $comp_only = $row->id;
                        break;
                    }
                }
            }
        
            return $comp_only;
        }

        private function get_created_comp() {
            $created_comp = $this->model->get_many_where('status', 'created', 'comp_name');
            return $created_comp;
        }

        private function get_show_only() {
            $show_only_val = segment(3); // PEISTI i 3 kad veiktu su www
            switch($show_only_val) {
                case '':
                    $show_only = '';
                    break;
                case 'male_u12':
                    $show_only = 'Male U12';
                    break;
                case 'male_u15':
                    $show_only = 'Male U15';
                    break;
                case 'male_u18':
                    $show_only = 'Male U18';
                    break;
                case 'female_u12':
                    $show_only = 'Female U12';
                    break;
                case 'female_u15':
                    $show_only = 'Female U15';
                    break;
                case 'female_u18':
                    $show_only = 'Female U18';
                    break;
            }
            return $show_only;
        }

//----------------- PARTICIPANTS END ----------------------------
//---------------------------------------------------------------

//---------------------------------------------------------------
//------------------ COMPETITION EVENT --------------------------

        function create_comp() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            $user_id = $this->_get_organizer_user_id();

            $sql = "SELECT * FROM comp_name WHERE NOT status = 'finished' AND user_id = $user_id ORDER BY id DESC";
            $data['rows'] = $this->model->query($sql, 'object');

            $data['num_rows'] = count($data['rows']);

            // Fetch all divisions from comp_divisions
            $data['divisions'] = $this->model->get('name ASC', 'comp_divisions');
            $this->view('create_comp', $data);
        }

        function submit_create_comp() {

            $this->validation->set_rules('name', 'name', 'required|min_length[6]|max_length[55]');
            $this->validation->set_rules('year', 'year', 'required|exact_length[4]|integer');
            $this->validation->set_rules('location', 'location', 'required|min_length[6]|max_length[55]');
            
            $result = $this->validation->run(); //returns true or false
            
            $update_id = segment(3, 'int');

            if($result === true) {
                // Create new competition.
                // Now build up array of $data for the comp record.
                $data['name'] = post('name', true);
                $data['year'] = post('year', true);
                $data['location'] = post('location', true);
                
                $user_id = $this->_get_organizer_user_id();
                $data['user_id'] = $user_id; // Set

                $divisions = post('divisions', true);

                if ($update_id>0) {
                    //update an existing record
                    $data['status'] = post('status', true);
                    $this->model->update($update_id, $data, 'comp_name');
                    $flash_msg = 'The record was successfully updated!';
                    echo '<p style="color:white;background:green;text-align: center;padding: 0.5rem;">' . $flash_msg . '</p>';
                } else {
                    //insert the new record
                    $update_id = $this->model->insert($data, 'comp_name');
                    
                    // Insert into comp_competition_divisions
                    if (is_array($divisions)) {
                        foreach ($divisions as $division_id) {
                            $link_data = [
                                'competition_id' => $update_id,
                                'division_id' => $division_id
                            ];
                            $this->model->insert($link_data, 'comp_competition_divisions');
                        }
                    }
                    $flash_msg = 'The record was successfully created!';
                    echo '<p style="color:white;background:green;text-align: center;padding: 0.5rem;">' . $flash_msg . '</p>';
                }

            } else {
                $flash_msg = 'Form fields have to be filled!';
                echo '<p style="color:black;background:orange;text-align:center;padding:0.5rem;">' . $flash_msg . '</p>';
            }
        }

        function edit_comp() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            $record_id = (int) segment(3);
            
            $comp = $this->model->get_where($record_id, 'comp_name');
            $data = (array)$comp; 

            $this->view('edit_comp', $data);
        }

        public function submit_delete_comp() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');
            $record_id = (int)segment(3);
            $this->model->delete($record_id, 'comp_name');
            echo '<p style="color:white;background:green;text-align: center;padding: 0.5rem;">Competition with id ' . $record_id . ' was deleted!</p>';
        }

        public function delete_modal() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');
            $update_id = (int) segment(3);
            $data['update_id'] = $update_id;
            $this->view('delete_modal', $data);
        }

        public function get_competitions() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            $user_id = $this->_get_organizer_user_id();

            if ($user_id === 0) {
                $user = $this->_get_judge_info();
                // If the user is an admin, we can return all competitions created by same organizer
                if ($user->role !== 'admin') {
                    $user_id = 0;
                } else {
                    $user_id = $user->organizer_id;
                }
            }

            $comp = $this->model->get_many_where('user_id', $user_id, 'comp_name');
            return $comp;
        }

//------------------ COMPETITION EVENT END-----------------------
//---------------------------------------------------------------

//---------------------------------------------------------------
//----------------------- JUDGES --------------------------------

        function create_judge() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            $organizer_id = $this->_get_organizer_user_id();

            $data['rows'] = $this->model->get_many_where('organizer_id', $organizer_id, 'comp_judges');
            $data['view_file'] = 'create_judge';
            $this->template('judges_area', $data);
        }

        function judge_profile() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            $user = $this->_get_judge_info();
            $user_id = (int)$user->trongate_user_id;

            $tables = ['comp_judges', 'comp_users'];
            $user_obj = false;

            foreach ($tables as $table) {
                $user_obj = $this->model->get_one_where('trongate_user_id', $user_id, $table);
                if ($user_obj !== false) {
                    break;
                }
            }

            $data = (array)$user_obj;
            $data['view_file'] = 'judge_profile';
            $this->template('judges_area', $data);
        }

        function delete_judge_modal() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            $update_id  = (int) segment(3);

            $judge = $this->model->get_where($update_id, 'comp_judges', 'object');

            $data['name'] = $judge->name;
            $data['update_id'] = $update_id;

            $this->view('delete_judge_modal', $data);
        }

        function edit_judge_modal() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            $update_id = (int) segment(3);
            $record_obj = $this->model->get_where($update_id, 'comp_judges');
            $data = (array)$record_obj;
            $this->view('edit_judge', $data);
        }

        function submit_edit_judge() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            $this->validation->set_rules('name', 'name', 'required|min_length[6]|max_length[55]');
            $this->validation->set_rules('role', 'role', 'required|min_length[5]|max_length[5]');

            $result = $this->validation->run(); //returns true or false
            $record_id = (int) segment(3);

            if($result === true) {

                // Now build up array of $data for the judges record.
                $data['name'] = post('name', true);
                $data['role'] = post('role', true);
                $this->model->update($record_id, $data, 'comp_judges');
                echo '<p style="color: black;background: rgb(92, 142, 141);text-align: center;padding: 0.5rem;">Judge updated succesfully!</p>';

            } else {

                echo '<p style="color: black;background: orange;text-align: center;padding: 0.5rem;">Form fields have to be filled!</p>';

            }
        }

        function submit_delete_judge() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            $update_id = (int) segment(3);

            $this->model->delete($update_id, 'comp_judges');
            set_flashdata('<p>Judge record deleted successfully!</p>');
            http_response_code(200);
        }

        function login() {
            $data['username'] = post('username');
            $data['view_file'] = 'judges_login';
            $this->template('public', $data);
        }

        function logout() {
            $this->module('trongate_tokens');
            $this->trongate_tokens->_destroy();
            redirect('competitions/login');
        }

        function submit_create_judge() {

            $this->validation->set_rules('name', 'name', 'required|min_length[6]|max_length[55]');
            $this->validation->set_rules('username', 'username', 'required|min_length[6]|max_length[55]|callback_username_unique');
            $this->validation->set_rules('password', 'password', 'required|min_length[6]|max_length[55]');
            $this->validation->set_rules('role', 'role', 'required|min_length[5]|max_length[5]');
            $this->validation->set_rules('repeat_password', 'repeat password', 'required|matches[password]');
            $this->validation->set_rules('organizer', 'organizer', 'required|integer');

            $result = $this->validation->run(); //returns true or false
        
            if($result === true) {
                // Create new member account.

                // Start by creating a new record on Trongate users.
                $trongate_user_data['code'] = make_rand_str(32);
                $trongate_user_data['user_level_id'] = 3; // judge id.
                $trongate_user_id = $this->model->insert($trongate_user_data, 'trongate_users');

                // Now build up array of $data for the judges record.
                $data['name'] = post('name', true);
                $data['username'] = post('username', true);
                $password = post('password');
                $data['role'] = post('role', true);
                $data['password'] = $this->hash_string($password);
                $data['trongate_user_id'] = $trongate_user_id;
                $data['organizer_id'] = post('organizer', true);

                // Create new members record in dattabase.
                $this->model->insert($data, 'comp_judges');
                
                echo '<p style="color: black;background: rgb(92, 142, 141);text-align: center;padding: 0.5rem;"> Congrats... Judge created succesfully!</p>';

            } else {

                echo '<p style="color: black;background: orange;text-align: center;padding: 0.5rem;">Form fields have to be filled!</p>';

            }
        }

        function submit_login() {
            $this->validation->set_rules('username', 'username', 'required|callback_login_check');
            $this->validation->set_rules('password', 'password', 'required');
        
            $result = $this->validation->run(); // Returns true or false.
        
            if($result === false) {
                $this->login();
            } else {
                $username = post('username');
                $remember = (int) post('remember');
                $this->_in_you_go($username, $remember);
            }
        }

        function _get_user_data($username) {
            // Get user data from comp_judges or comp_users table.
            $tables = ['comp_judges', 'comp_users'];
            $member_obj = false;

            foreach ($tables as $table) {
                $member_obj = $this->model->get_one_where('username', $username, $table);
                if ($member_obj !== false) {
                    break;
                }
            }

            return $member_obj;
        }

        function _in_you_go($username, $remember) {
            // Get trongate_user_id for this user.
            $member_obj = $this->_get_user_data($username);

            $trongate_user_id = $member_obj->trongate_user_id;

            // Create trongate token using token model.
            $this->module('trongate_tokens');
            $trongate_token_data['user_id'] = $trongate_user_id;

            if($remember === 1) {
                $trongate_token_data['set_cookie'] = true;
            }

            $this->trongate_tokens->_generate_token($trongate_token_data);

            redirect('competitions');

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
            $member_obj = $this->model->get_one_where('username', $username, 'comp_judges');
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
            $member_obj = $this->_get_user_data($username);

            if ($member_obj === false) {
                return $error_msg; // Username does not exist.
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

        function _make_sure_allowed() {
            //Make sure the user is loged in as a judge
            $this->module('trongate_tokens');
            $token = $this->trongate_tokens->_attempt_get_valid_token([3,4]);
       
            if($token === false) {
                redirect('competitions/login');
            } else {
                return $token;
            }
        }

        function _get_judge_info() {
            $this->module('trongate_tokens');
            $trongate_user_id = $this->trongate_tokens->_get_user_id();

            $tables = ['comp_judges', 'comp_users'];
            $judge_info = false;

            foreach ($tables as $table) {
                $judge_info = $this->model->get_one_where('trongate_user_id', $trongate_user_id, $table);
                if ($judge_info !== false) {
                    break;
                }
            }

            return $judge_info;
        }

        function _get_organizer_user_id() {
            $user = $this->_get_judge_info();
            return ($user && $user->role === 'organizer') ? $user->id : 0;
        }

//----------------------- JUDGES END ----------------------------
//---------------------------------------------------------------

//---------------------------------------------------------------
//--------------------- RESULTS BEGIN ---------------------------

        function get_final_results($heat_id) {
            // Step 1: Get average scores per wave
            $sql = "SELECT participant_id, wave_number, AVG(score) AS avg_wave_score
                    FROM comp_judge_scores
                    WHERE heat_id = ?
                    GROUP BY participant_id, wave_number";
            $data = [$heat_id];
            $wave_scores = $this->model->query_bind($sql, $data, 'array');

            // Step 2: Organize scores by participant
            $participant_scores = [];
            foreach ($wave_scores as $row) {
                $participant_scores[$row['participant_id']][] = $row['avg_wave_score'];
            }

            // Step 3: Get the sum of the top 2 waves per participant
            $final_results = [];
            foreach ($participant_scores as $participant_id => $scores) {
                rsort($scores); // Sort in descending order
                $top_2_waves = array_slice($scores, 0, 2); // Get top 2 waves
                $final_results[] = [
                    'participant_id' => $participant_id,
                    'final_score' => array_sum($top_2_waves),
                ];
            }

            // Step 4: Rank participants by final score (highest to lowest)
            usort($final_results, function ($a, $b) {
                return $b['final_score'] <=> $a['final_score'];
            });

            return $final_results;
        }

        function adjust_scores_for_outliers($heat_id) {
            // Fetch average score and standard deviation per wave
            $sql = "SELECT wave_number, AVG(score) AS avg_score, STDDEV(score) AS std_dev
                    FROM comp_judge_scores
                    WHERE heat_id = ?
                    GROUP BY wave_number";
            $data = [$heat_id];
            $wave_stats = $this->model->query_bind($sql, $data, 'array');

            $adjusted_scores = [];

            foreach ($wave_stats as $wave) {
                $wave_number = $wave['wave_number'];
                $mean = $wave['avg_score'];
                $std_dev = $wave['std_dev'];
                $threshold = $std_dev * 1.5; // Set outlier threshold (1.5x SD)

                // Fetch individual scores for this wave
                $sql = "SELECT id, participant_id, score FROM comp_judge_scores WHERE heat_id = ? AND wave_number = ?";
                $data = [$heat_id, $wave_number];
                $scores = $this->model->query_bind($sql, $data, 'array');

                foreach ($scores as $score) {
                    $score_id = $score['id'];
                    $participant_id = $score['participant_id'];
                    $raw_score = $score['score'];

                    // Adjust scores if they are outliers
                    if (abs($raw_score - $mean) > $threshold) {
                        $adjusted_score = ($raw_score + $mean) / 2; // Bring it closer to the average
                    } else {
                        $adjusted_score = $raw_score;
                    }

                    // Save adjusted scores
                    $adjusted_scores[] = [
                        'id' => $score_id,
                        'participant_id' => $participant_id,
                        'wave_number' => $wave_number,
                        'original_score' => $raw_score,
                        'adjusted_score' => round($adjusted_score, 2)
                    ];
                }
            }

            return $adjusted_scores;
        }

        function adjust_scores() {
            $heat_id = post('heat_id');
            $wave_number = post('wave_number');

            // Check if scores exist for this wave
            $sql = "SELECT COUNT(*) AS count FROM comp_judge_scores WHERE heat_id = ? AND wave_number = ?";
            $score_count = $this->model->query_bind($sql, [$heat_id, $wave_number], 'single')->count;

            $this->_adjust_and_save_scores($heat_id, $wave_number);
            redirect('admin/view_scores');
        }

        function _adjust_and_save_scores($heat_id, $wave_number) {
            // Get average and standard deviation
            $sql = "SELECT participant_id, AVG(score) AS avg_score, STDDEV(score) AS std_dev 
                    FROM comp_judge_scores WHERE heat_id = ? AND wave_number = ? GROUP BY participant_id";
            $wave_stats = $this->model->query_bind($sql, [$heat_id, $wave_number], 'array');

            foreach ($wave_stats as $wave) {
                $participant_id = $wave['participant_id'];
                $mean = $wave['avg_score'];
                $std_dev = $wave['std_dev'];
                $threshold = $std_dev * 1.5;

                // Fetch scores for adjustment
                $sql = "SELECT id, score FROM comp_judge_scores WHERE heat_id = ? AND wave_number = ? AND participant_id = ?";
                $scores = $this->model->query_bind($sql, [$heat_id, $wave_number, $participant_id], 'array');

                $adjusted_scores = [];
                foreach ($scores as $score) {
                    $score_id = $score['id'];
                    $raw_score = $score['score'];

                    // Adjust outliers
                    if (abs($raw_score - $mean) > $threshold) {
                        $adjusted_score = ($raw_score + $mean) / 2;
                    } else {
                        $adjusted_score = $raw_score;
                    }

                    // Update in `comp_judge_scores`
                    $update_sql = "UPDATE comp_judge_scores SET score = ? WHERE id = ?";
                    $this->model->query_bind($update_sql, [round($adjusted_score, 2), $score_id]);

                    $adjusted_scores[] = $adjusted_score;
                }

                // Compute final average
                $final_avg = round(array_sum($adjusted_scores) / count($adjusted_scores), 2);

                // Save to `comp_wave_averages`
                $insert_sql = "INSERT INTO comp_wave_averages (heat_id, wave_number, participant_id, avg_score)
                            VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE avg_score = ?";
                $this->model->query_bind($insert_sql, [$heat_id, $wave_number, $participant_id, $final_avg, $final_avg]);
            }
        }

//----------------------- RESULTS END ---------------------------
//---------------------------------------------------------------

//---------------------------------------------------------------
//-------------------- EDITING JUDGE SCORES ---------------------
        
        function edit_scores() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            // Get all available heats for dropdown
            $all_heats = $this->model->query("SELECT ch.id, division, round, heat_number, ch.status FROM comp_heats ch JOIN comp_name cn ON ch.comp_id = cn.id WHERE ch.status IN ('finished', 'running') AND cn.status = 'running' ORDER BY division ASC;", 'array');
            $data['all_heats'] = $all_heats;

            $heat_id = (int) segment(3);

            if (isset($heat_id)) {

                // Get all scores for this heat
                $sql = "SELECT s.id, s.judge_id, s.participant_id, s.wave_number, s.score, 
                            p.first_name, p.last_name, h.jersey_color 
                        FROM comp_judge_scores s
                        JOIN comp_participants p ON s.participant_id = p.id
                        JOIN comp_heat_participants h ON h.participant_id = p.id AND h.heat_id = s.heat_id
                        WHERE s.heat_id = ?
                        ORDER BY s.participant_id, s.wave_number";

                $scores = $this->model->query_bind($sql, [$heat_id], 'array');

                $data['heat_id'] = $heat_id;
                $data['scores'] = $scores;

            }

            $data['view_file'] = 'edit_scores';
            $this->template('judges_area', $data);

        }

        function edit_running_scores() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            // Fetch currently running heat
            $heat = $this->_get_running_heat();

            if (!isset($heat)) {

                echo "No running heats available.";
                $data['error'] = "No running heats for edit yet!";
                $data['view_file'] = 'heat_edit_error';
                $this->template('judges_area', $data);
                exit();
            }

            $heat_id = $heat->id;

            // Get all scores for this heat
            $sql = "SELECT s.id, s.judge_id, s.participant_id, s.wave_number, s.score, 
                        p.first_name, p.last_name, h.jersey_color 
                    FROM comp_judge_scores s
                    JOIN comp_participants p ON s.participant_id = p.id
                    JOIN comp_heat_participants h ON h.participant_id = p.id AND h.heat_id = s.heat_id
                    WHERE s.heat_id = ?
                    ORDER BY s.participant_id, s.wave_number";

            $scores = $this->model->query_bind($sql, [$heat_id], 'array');

            $data['heat_id'] = $heat_id;
            $data['all_heats'] = $all_heats;
            $data['scores'] = $scores;
            $data['view_file'] = 'edit_scores';
            $this->template('judges_area', $data);
        }

        function update_score() {
            $score_id = (int) segment(3);
            $new_score = post('score');

            if (is_numeric($new_score)) {
                $this->model->update($score_id, ['score' => $new_score], 'comp_judge_scores');
            } else {
                echo "Invalid score value.";
                return;
            }

            // Get heat_id and wave_number from the score record
            $score_record = $this->model->get_where($score_id, 'comp_judge_scores');
            if (!$score_record) {
                echo "Score record not found.";
                return;
            }
            $heat_id = $score_record->heat_id;
            $wave_number = $score_record->wave_number;
            $participant_id = $score_record->participant_id;

            $this->_calculate_and_save_average($heat_id, $wave_number, $participant_id);

            echo '<p style="padding: 5px;text-align: center;">Score updated to ' . $new_score . ' successfully.</p>';

        }  

    }
?>