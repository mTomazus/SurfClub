<?php
    class Competitions extends Trongate {
        
        function index() {
            $this->judge_dash();
        }

        function success() {
            $this->view('success');
        }

        function judge_dash() {
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

        function admin_dash() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');
            
            $judge = $this->_get_judge_info();
            $data['user'] = $judge;


            if ($judge->role === 'admin') {
                $data['view_file'] = 'admin_dash';
                $this->template('judges_area', $data);
            } else {
                $data['view_file'] = 'judge_dash';
                $this->template('judges_area', $data);
            }
        }

        function heat_time() {
            date_default_timezone_set('Europe/Vilnius');

            $heat = $this->model->get_one_where('status', 'running', 'comp_heats');
            $future_time = new DateTime($heat->end_time); // Convert string to DateTime
            $current_time = new DateTime(); // Get current time

            if ($future_time > $current_time) {
                $interval = $current_time->diff($future_time);
                echo $interval->format('%i:%s');  // Outputs MM:SS
            } else {
                echo "00:00"; // Timer has expired
            }
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
                $data['error'] = "No scores yet!";
                $data['heat'] = $this->model->get_where_custom('status', 'scheduled', '=', 'start_time ASC', 'comp_heats', 1);
                $this->view('error', $data);
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
                $data['error'] = "No running heats yet!";
                $data['heat'] = $this->model->get_where_custom('status', 'scheduled', '=', 'start_time ASC', 'comp_heats', 1);
                $data['view_file'] = 'error';
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
            $flash_msg = 'The record was successfully created';

            // After submit to database NextWave becomes WaveNumber
            $wave_number = $next_wave;

            $this->_calculate_and_save_average($heat_id, $wave_number, $participant_id);
   
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

            // Calculate the average
            $average_score = round($total_score / $total_judges, 2); // Round to 2 decimal places
        
            // Check if the average score already exists for this wave and participant
            $sql_existing = "SELECT * FROM comp_wave_averages WHERE heat_id = ? AND wave_number = ? AND participant_id = ?";
            $existing_data = [$heat_id, $wave_number, $participant_id];
            $existing = $this->model->query_bind($sql_existing, $existing_data, 'array');  // Change 'object' to 'array'

            // Insert or update the average score in the `comp_wave_averages` table
            if ($existing) {
                $this->model->update($existing->id, ['avg_score' => $average_score], 'comp_wave_averages');
            } else {

                $data_avg = [
                    'heat_id' => $heat_id,
                    'wave_number' => $wave_number,
                    'participant_id' => $participant_id,
                    'avg_score' => $average_score
                ];

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
            $rows = $this->model->get('id asc', 'comp_name');
            $data['rows'] = $rows;
            $data['view_file'] = 'create_participant';
            $this->template('public', $data);
        }

        function edit_participant() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            $record_id = (int) segment(3);
            
            $record_obj = $this->model->get_where($record_id, 'comp_participants');
            $competitions = $this->model->get('id DESC', 'comp_name');

            $data = (array)$record_obj;
            $data['rows'] = $competitions;
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
            $show_only = $this->get_show_only();
            if($show_only == '') {
                $data['rows'] = $this->model->get('gender_age', 'comp_participants');
            } else {
                $data['rows'] = $this->model->get_many_where('gender_age', $show_only, 'comp_participants');
            }

            $judge = $this->_get_judge_info();
            $data['user'] = $judge;

            $data['view_file'] = 'show_participants';
            $this->template('judges_area', $data);
        }

        function submit_create_participant() {

            $this->validation_helper->set_rules('first_name', 'First Name', 'required|min_length[4]|max_length[55]');
            $this->validation_helper->set_rules('last_name', 'Last Name', 'required|min_length[4]|max_length[55]');
            $this->validation_helper->set_rules('email', 'Email', 'required|valid_email');
            $this->validation_helper->set_rules('comp_id', 'comp_id', 'required|min_length[1]|integer');

            $result = $this->validation_helper->run(); //returns true or false
            
            $update_id = segment(3, 'int');
        
            if($result === true) {
    
                // Now build up array of $data for the comp_participants record.
                $data['first_name'] = post('first_name', true);
                $data['last_name'] = post('last_name', true);
                $data['email'] = post('email', true);
                $data['comp_id'] = post('comp_id', true);
                $gender = post('gender', true);
                $age = post('age_group', true);
                $data['gender_age'] = $gender . ' ' . $age;

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
            $data['rows'] = $this->model->get('id desc', 'comp_name');
            $data['num_rows'] = $this->model->count('comp_name');
            $this->view('create_comp', $data);
        }

        function comp_created() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');
            $data['view_file'] = 'comp_created';
            $this->template('judges_area', $data);
        }

        function submit_create_comp() {

            $this->validation_helper->set_rules('name', 'name', 'required|min_length[6]|max_length[55]');
            $this->validation_helper->set_rules('year', 'year', 'required|exact_length[4]|integer');
            $this->validation_helper->set_rules('location', 'location', 'required|min_length[6]|max_length[55]');

            $result = $this->validation_helper->run(); //returns true or false
        
            if($result === true) {
                // Create new competition.
                // Now build up array of $data for the comp record.
                $data['name'] = post('name', true);
                $data['year'] = post('year', true);
                $data['location'] = post('location', true);

                // Create new comp record in database.
                $this->model->insert($data, 'comp_name');

                echo '<p style="color:white;background:green;text-align: center;padding: 0.5rem;">Competition was added!</p>';

            } else {
                echo '<p style="color: black;background: orange;text-align: center;padding: 0.5rem;">Form fields have to be filled!</p>';
            }
        }

//------------------ COMPETITION EVENT END-----------------------
//---------------------------------------------------------------

//---------------------------------------------------------------
//----------------------- JUDGES --------------------------------

        function create_judge() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');
            $data['view_file'] = 'create_judge';
            $this->template('judges_area', $data);
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

            $this->validation_helper->set_rules('name', 'name', 'required|min_length[6]|max_length[55]');
            $this->validation_helper->set_rules('username', 'username', 'required|min_length[6]|max_length[55]|callback_username_unique');
            $this->validation_helper->set_rules('password', 'password', 'required|min_length[6]|max_length[55]');
            $this->validation_helper->set_rules('repeat_password', 'repeat password', 'required|matches[password]');

            $result = $this->validation_helper->run(); //returns true or false
        
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
                $data['password'] = $this->hash_string($password);
                $data['trongate_user_id'] = $trongate_user_id;

                // Create new members record in dattabase.
                $this->model->insert($data, 'comp_judges');
                
                set_flashdata("Judge Created Succesfully!");

                redirect('competitions/judges_login');

            } else {

                echo '<p style="color: black;background: orange;text-align: center;padding: 0.5rem;">Form fields have to be filled!</p>';

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
            $member_obj = $this->model->get_one_where('username', $username, 'comp_judges');
            $trongate_user_id = $member_obj->trongate_user_id;

            // Create trongate token using token model.
            $this->module('trongate_tokens');
            $trongate_token_data['user_id'] = $trongate_user_id;

            if($remember === 1) {
                $trongate_token_data['set_cookie'] = true;
            }

            $this->trongate_tokens->_generate_token($trongate_token_data);

            // Send the user to private judge's or admin area.
            if ($member_obj->role == 'admin') {
                redirect('competitions/admin_dash');
            } else {
                redirect('competitions/judge_dash'); 
            }
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
            $member_obj = $this->model->get_one_where('username', $username, 'comp_judges');
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

        function _make_sure_allowed() {
            //Make sure the user is loged in as a judge
            $this->module('trongate_tokens');
            $token = $this->trongate_tokens->_attempt_get_valid_token(3);
       
            if($token === false) {
                redirect('competitions/login');
            } else {
                return $token;
            }
        }

        function _get_judge_info() {
            $this->module('trongate_tokens');
            $trongate_user_id = $this->trongate_tokens->_get_user_id();
            $judge_info = $this->model->get_one_where('trongate_user_id', $trongate_user_id, 'comp_judges');
            return $judge_info;
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
    }
?>