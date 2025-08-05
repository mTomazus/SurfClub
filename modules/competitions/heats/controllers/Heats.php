<?php
    class Heats extends Trongate {

        function index() {

            $competitions = $this->model->get('id', 'comp_name');
            $data = [
                "competitions" => $competitions,
                "view_file" => "show_comps"
            ];
            $this->template('public', $data);
        }

        function generate_modal() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            $comp_id = segment(3);
            $data['comp_id'] = $comp_id;

            $data['view_file'] = 'generate_modal';
            $this->template('judges_area', $data);
        }

        function heat_generation_page() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            $sql = "SELECT id, name, year FROM comp_name WHERE status = 'closed' ORDER BY year DESC";
            $results = $this->model->query($sql, 'array');
            $data['rows'] = $results;

            $data['view_file'] = 'heats_generate';
            $this->template('judges_area', $data);
        }

        function generate_all_heats() {

            if (segment(3) !== 0) {
                $comp_id = segment(3); // Get competition ID from URL
            } else if (isset($_GET['comp_id'])) {
                $comp_id = $_GET['comp_id']; // Get competition ID from GET request
            } else {
                die("No competition selected.");
            }

            // Fetch competition name
            $competition = $this->model->get_one_where('id', $comp_id, 'comp_name');

            $comp_name = $competition->name . ' ' . $competition->year;

            $sql = "SELECT d.name
                        FROM comp_competition_divisions cd
                        JOIN comp_divisions d ON cd.division_id = d.id
                        WHERE cd.competition_id = $comp_id";

            $div = $this->model->query($sql, 'array');
            $divisions = array_column($div, 'name');
            //$divisions = ["Male U12", "Male U15", "Male U18", "Female U12", "Female U15", "Female U18", "Male ADT", "Female ADT", "Male VET", "Female VET"];

            foreach ($divisions as $division) {
                // Get participants in this competition and division
                // Build the SQL query (using placeholders).
                $sql = "SELECT id FROM comp_participants WHERE comp_id = :comp_id AND gender_age = :division";

                // Named parameters to bind to the query.
                $data = [
                    'comp_id' => $comp_id,
                    'division' => $division
                ];

                // Execute the query using the named parameters.
                $participants = $this->model->query_bind($sql, $data, 'array');

                // Shuffle participants for fair random seeding
                shuffle($participants);

                // Determine the number of heats needed
                $total_participants = count($participants);

                // Maximum participants per heat
                if ($total_participants = 4) {

                    echo "<h3>Started Generating heats =4 for " . out($division);
                    $four = $this->generate_four($comp_id, $participants, $total_participants, $division);
                    echo "<h3>Heats Generated Successfully for " . out($division);

                } elseif ($total_participants <= 6) {

                    echo "<h3>Started Generating heats <=6 for " . out($division);
                    $six = $this->generate_six($comp_id, $participants, $total_participants, $division);
                    echo "<h3>Heats Generated Successfully for " . out($division);
                
                } elseif ($total_participants <= 8) {

                    echo "<h3>Started Generating heats <=8 for " . out($division);
                    $six = $this->generate_eight($comp_id, $participants, $total_participants, $division);
                    echo "<h3>Heats Generated Successfully for " . out($division);
                
                } elseif ($total_participants <= 9) {

                    echo "<h3>Started Generating heats <=9 for " . out($division);
                    $nine = $this->generate_nine($comp_id, $participants, $total_participants, $division);
                    echo "<h3>Heats Generated Successfully for " . out($division);

                } elseif ($total_participants <= 12) {

                    echo "<h3>Started Generating heats <=12 for " . out($division);
                    $twelve = $this->generate_twelve($comp_id, $participants, $total_participants, $division);
                    echo "<h3>Heats Generated Successfully for " . out($division);

                } elseif ($total_participants <= 16) {

                    echo "<h3>Started Generating heats <=16 for " . out($division);
                    $nine = $this->generate_sixteen($comp_id, $participants, $total_participants, $division);
                    echo "<h3>Heats Generated Successfully for " . out($division);

                } 
                
            }

            echo "<h2>Heats Generated Successfully for " . out($comp_name);
            $this->module->update($comp_id, ['status' => 'generated'], 'comp_name'); 
            redirect('competitions-heats/show_heats_draw/' . $comp_id);
        }

        function heat_schedule_page() {
            $this->module('trongate_security');
            $this->trongate_security->_make_sure_allowed('judges area');

            $sql = "SELECT ch.*, CONCAT(cn.name, ' ', cn.year) AS name
                    FROM comp_heats ch
                    JOIN comp_name cn ON ch.comp_id = cn.id
                    WHERE ch.status IN ('pending', 'scheduled')
                    ORDER BY ch.id, ch.heat_number;";

            $all_heats = $this->model->query($sql, 'object');

            $data = [
                'heats' => $all_heats,
                "view_file" => "heat_schedule_form"
            ];
            $this->template('judges_area', $data);
        }

        function update_heat_schedule() {

            // Validate time inputs
            $this->validation->set_rules('start_time', 'start_time', 'required');
            $this->validation->set_rules('heat-length', 'heat-length', 'required');

            $result = $this->validation->run();
            
            if ($result == true) {

                $start_time = post('start_time');
                $length = post('heat-length');
                $heat_id = post('heat_id');

                $end_time = date('Y-m-d H:i', strtotime($start_time . " +$length minutes"));
            
                // Prepare data for update
                $data = [
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'status' => 'scheduled'
                ];
            
                // Update the heat record in the database
                $update_success = $this->model->update($heat_id, $data, 'comp_heats');
            
                return $update_success ? "Heat updated successfully!" : "Error updating heat.";
            }
            echo "<p style='color:red'>Validation error</p>";
        }

        function show_heats_draw() {
            // Get competition ID from URL
            $comp_id = segment(3) !== '' ? segment(3) : null;

            // Get the filter for showing only certain divisions
            $show_only = $this->get_show_only();
        
            // Fetch all heats
            $heats = $this->get_all_heats($comp_id, $show_only);
            $comp_name = $this->get_comp_name($comp_id);

            //Fetch unique divisions
            $unique_divisions = $this->get_unique_divisions($comp_id);

            // Fetch heat results from comp_heat_results
            $sql = "SELECT * FROM comp_heat_results WHERE heat_id IN 
            (SELECT id FROM comp_heats WHERE comp_id = ?)";
            $heat_results = $this->model->query_bind($sql, [$comp_id], 'array');

            // Organize results by heat_id and participant_id for easier access
            $formatted_results = [];
            if (!empty($heat_results)) {
                foreach ($heat_results as $result) {
                    $formatted_results[$result['heat_id']][$result['participant_id']] = $result;
                }
            }

            // Loop through heats and fetch participants + results
            foreach ($heats as &$heat) { // Use reference to modify array

                $heat['participants'] = $this->get_heat_participants($heat['id']) ?? [];

                foreach ($heat['participants'] as &$participant) {
                    $participant_id = (int)$participant['id'];

                    // Assign result safely, avoiding undefined index error
                    $participant['result'] = isset($formatted_results[$heat['id']][$participant_id]) 
                        ? $formatted_results[$heat['id']][$participant_id] 
                        : null;
                }
            }
            
            // Pass data to view
            $data = [
                'comp_name' => $comp_name,
                'comp_id' => $comp_id,
                'heats' => $heats,
                'view_file' => 'show_draw'
            ];
        
            $this->template('public', $data);
        }

        function show_heats() {
            // Get competition ID from URL
            $comp_id = segment(3) !== '' ? segment(3) : null;

            $this->module('competitions');
            $judge = $this->competitions->_get_judge_info();

            // Get the filter for showing only certain divisions
            $show_only = $this->get_show_only();
        
            // Fetch all heats
            $heats = $this->get_all_heats($comp_id, $show_only);
            $comp_name = $this->get_comp_name($comp_id);

            //Fetch unique divisions
            $unique_divisions = $this->get_unique_divisions($comp_id);

            // Loop through heats and fetch participants
            foreach ($heats as &$heat) { // Use reference to modify array

                $heat['participants'] = $this->get_heat_participants($heat['id']);
            
            }
            
            // Pass data to view
            $data = [
                'comp_name' => $comp_name,
                'user' => $judge,
                'heats' => $heats,
                'view_file' => 'show_heats',
            ];

            $this->template('judges_area', $data);
        }

        private function get_comp_name($comp_id) {
            // Fetch competition name
            $competition = $this->model->get_one_where('id', $comp_id, 'comp_name');
            $comp_name = $competition->name . ' ' . $competition->year;
            return $comp_name;
        }

        private function get_all_heats($comp_id, $show_only) {

            // Base SQL query
            $sql = "SELECT * FROM comp_heats WHERE comp_id = ?";

            // If $show_only is not empty, add the division filter
            $params = [$comp_id];
            if (!empty($show_only)) {
                $sql .= " AND division = ?";
                $params[] = $show_only;
            }
            $sql .= " ORDER BY id, heat_number";
        
            // Execute the query with bound parameters
            $heats = $this->model->query_bind($sql, $params, 'array');

            return $heats;
        }

        private function get_heat_participants($heat_id){

            // Build the SQL query (using placeholders).
            $sql = "SELECT p.id, p.first_name, p.last_name, hp.jersey_color
            FROM comp_heat_participants hp
            JOIN comp_participants p ON hp.participant_id = p.id
            WHERE hp.heat_id = $heat_id
            ORDER BY FIELD(hp.jersey_color, 'white', 'red', 'green', 'blue')";

            // Execute the query using the unnamed parameters.
            $heat_participans = $this->model->query($sql, 'array');

            return $heat_participans;
        }

        private function get_unique_divisions($comp_id) {

            $sql = "SELECT DISTINCT division FROM comp_heats WHERE comp_id = ?";
            $divisions = $this->model->query_bind($sql, [$comp_id], 'array');

            return array_column($divisions, 'division');
        }

        function get_show_only() {
            $show_only_val = segment(4); // PEISTI i 4 kad veiktu su www
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

//-------------------------------------------------------------
//------------------   FINAL SCORES   -------------------------
//-------------------------------------------------------------
        function _process_heat_results($heat_id) {

            // Get sorted final scores (top 2 waves summed per participant)
            $final_scores = $this->_get_final_scores($heat_id);

            // Get division & heat info
            $heat_info = $this->model->get_where($heat_id, 'comp_heats');
            $division = $heat_info->division;
            $current_round = $heat_info->round;

            // Get total participants in division
            $division_size = $this->_get_division_size($heat_id);
            
            // Determine heat results (advancing, repechage, eliminated, finalists)
            $heat_results = $this->_determine_heat_results($division_size, $current_round, $final_scores);
            
            // Process advancing participants
            $this->_assign_next_round($heat_results['advancing'], $current_round, $division, $division_size);
            
            // Process repechage participants
            $this->_assign_repechage($heat_results['repechage'], $current_round, $division);
            
            // Save heat results to standings
            $this->_save_heat_results($heat_id, $final_scores);
            
            // Mark heat as finished
            $this->model->update($heat_id, ['status' => 'finished'], 'comp_heats');
        }

        function _get_division_size($heat_id) {
            $sql = "SELECT COUNT(*) AS total 
                    FROM comp_participants 
                    WHERE gender_age = (SELECT division 
                    FROM comp_heats WHERE id = ? )";
            $division_total = $this->model->query_bind($sql, [$heat_id], 'array');
            return (int)$division_total[0]['total'];
        }
        
        function _determine_heat_results($division_size, $current_round, $final_scores) {
            $advancing = [];
            $repechage = [];
            $eliminated = [];
            $finalists = [];
        
            if ($division_size <= 4) {
                $finalists = $final_scores;

            } elseif ($division_size <= 6) {
                if ($current_round == 'Round 1') {
                    $advancing[] = $final_scores[0];
                    $repechage = array_slice($final_scores, 1, 2);
                } elseif ($current_round == 'Repechage 1') {
                    $advancing[] = array_slice($final_scores, 0, 2);
                    $eliminated = array_slice($final_scores, 2, 2);
                }

            } elseif ($division_size <= 8) {
                if ($current_round == 'Round 1') {
                    $advancing = array_slice($final_scores, 0, 2);
                    $repechage = array_slice($final_scores, 2, 2);
                } elseif ($current_round == 'Repechage 1') {
                    $repechage = array_slice($final_scores, 0, 2);
                    $eliminated = array_slice($final_scores, 2, 2);
                } elseif ($current_round == 'Round 2') {
                    $advancing = array_slice($final_scores, 0, 2);
                    $repechage = array_slice($final_scores, 2, 2);
                } elseif ($current_round == 'Repechage 2') {
                    $advancing = array_slice($final_scores, 0, 2);
                    $eliminated = array_slice($final_scores, 2, 2);
                }

            } elseif ($division_size == 9) {
                if ($current_round == 'Round 1') {
                    $advancing[] = $final_scores[0];
                    $repechage = array_slice($final_scores, 1, 2);
                } elseif ($current_round == 'Repechage 1') {
                    $repechage[] = $final_scores[0];
                    $eliminated = array_slice($final_scores, 1, 2);
                } elseif ($current_round == 'Round 2') {
                    $advancing[] = $final_scores[0];
                    $repechage = array_slice($final_scores, 1, 2);
                } elseif ($current_round == 'Repechage 2') {
                    $advancing = array_slice($final_scores, 0, 2);
                    $eliminated = array_slice($final_scores, 2);
                }

            } elseif ($division_size <= 12) {
                if ($current_round == 'Round 1') {
                    $advancing[] = array_slice($final_scores, 0, 2);
                    $repechage = array_slice($final_scores, 2, 2);
                } elseif ($current_round == 'Repechage 1') {
                    $repechage[] = $final_scores[0];
                    $eliminated = array_slice($final_scores, 1, 2);
                } elseif ($current_round == 'Round 2') {
                    $advancing[] = $final_scores[0];
                    $repechage = array_slice($final_scores, 1, 2);
                } elseif ($current_round == 'Repechage 2') {
                    $advancing[] = $final_scores[0];
                    $eliminated = array_slice($final_scores, 1, 2);
                }

            } elseif ($division_size <= 16) {
                if ($current_round == 'Round 1') {
                    $advancing = array_slice($final_scores, 0, 2);
                    $repechage = array_slice($final_scores, 2, 2);
                } elseif ($current_round == 'Repechage 1') {
                    $repechage[] = $final_scores[0];
                    $eliminated = array_slice($final_scores, 1);
                } elseif ($current_round == 'Round 2') {
                    $advancing[] = $final_scores[0];
                    $repechage = array_slice($final_scores, 1);
                } elseif ($current_round == 'Repechage 2') {
                    $advancing[] = $final_scores[0];
                    $eliminated = array_slice($final_scores, 1);
                }
            }
        
            return [
                'advancing' => $advancing,
                'repechage' => $repechage,
                'eliminated' => $eliminated,
                'finalists' => $finalists
            ];
        }

        function _assign_next_round($advancing, $current_round, $division, $division_size) {
            foreach ($advancing as $participant) {

                $participant_id = $participant['participant_id'];
                if ($division_size <= 6) {
                    $next_round = 'Final';
                } else {
                    $next_round = ($current_round === 'Round 1') ? 'Round 2' : 'Final';
                }
                
                $was_current_round = $current_round;

                $this->_seed_next_round($participant_id, $was_current_round, $next_round, $division);
            }
        }
        
        function _assign_repechage($repechage, $current_round, $division) {

            if (!empty($repechage)) {
                foreach ($repechage as $participant) {
                    $participant_id = $participant['participant_id'];
                    $next_round = ($current_round === 'Round 1') ? 'Repechage 1' : 'Repechage 2';
                    $was_current_round = $current_round;
                    $this->_seed_next_round($participant_id, $was_current_round, $next_round, $division);
                }
            }
            
        }
        
        function _save_heat_results($heat_id, $final_scores) {
            foreach ($final_scores as $position => $data) {
                $rank = $position + 1;
                $save_data = [
                    'heat_id' => $heat_id,
                    'participant_id' => $data['participant_id'],
                    'total_score' => $data['total_score'],
                    'rank' => $rank
                ];
                $inserted = $this->model->insert($save_data, 'comp_heat_results');
                if (!$inserted) {
                    error_log("Failed to insert heat result for participant: " . $data['participant_id']);
                }
            }
        }

        function _get_final_scores($heat_id) {
            $sql = "SELECT participant_id, avg_score
                    FROM comp_wave_averages 
                    WHERE heat_id = ? 
                    ORDER BY participant_id, avg_score DESC";
            $results = $this->model->query_bind($sql, [$heat_id], 'array');

            $scores = [];
            foreach ($results as $row) {
                $id = $row['participant_id'];
                if (!isset($scores[$id])) $scores[$id] = [];
                if (count($scores[$id]) < 2) $scores[$id][] = $row['avg_score']; // Store top 2 waves
            }

            $final_scores = [];
            foreach ($scores as $id => $waves) {
                $final_scores[] = [
                    'participant_id' => $id,
                    'total_score' => array_sum($waves)  // Sum top 2 waves
                ];
            }
        
            usort($final_scores, fn($a, $b) => $b['total_score'] <=> $a['total_score']); // Sort DESC
            
            return $final_scores;
        }
        
        function _seed_next_round($participant_id, $was_current_round, $next_round, $division) {
            
            // Find all pending heats in next round for this division
            $sql = "SELECT id FROM comp_heats 
                    WHERE round = ? AND division = ? AND status = 'pending' 
                    ORDER BY id ASC";
            $data = [$next_round, $division];

            $next_heats = $this->model->query_bind($sql, $data, 'array');

            // Assign participants alternately into Heat 1 or Heat 2
            static $assignment_counter = 0;  // Tracks alternating heat assignments
            $heat_id = $next_heats[$assignment_counter % count($next_heats)]['id'];
            $assignment_counter++;
        
            // Prevent duplicate participant entries
            $check_sql = "SELECT COUNT(*) AS count FROM comp_heat_participants 
                          WHERE heat_id = ? AND participant_id = ?";
            $exists = $this->model->query_bind($check_sql, [$heat_id, $participant_id], 'array');
        
            if ($exists[0]['count'] == 0) { // Only insert if participant is not already seeded
                // Assign jersey color based on existing participants in this heat
                $colors = ['white', 'red', 'green', 'blue'];
        
                // Check which colors are already used in this heat
                $sql = "SELECT jersey_color FROM comp_heat_participants WHERE heat_id = ?";
                $used_colors = $this->model->query_bind($sql, [$heat_id], 'array');
                $used_colors = array_column($used_colors, 'jersey_color');
        
                // Get available jersey colors
                $available_colors = array_diff($colors, $used_colors);
                $jersey_color = !empty($available_colors) ? array_values($available_colors)[0] : null;

                if ($jersey_color) {
                    $data = [
                        'heat_id' => $heat_id,
                        'participant_id' => $participant_id,
                        'jersey_color' => $jersey_color,
                        'seeded_from' => $was_current_round
                    ];

                    // Insert participant into heat
                     $this->model->insert($data, 'comp_heat_participants');
                }
            }
        }        

        function calculate_final_standings($division_id) {
            
            $this->module('database');
        
            // Get all surfers in this division
            $sql = "SELECT DISTINCT participant_id FROM comp_heats WHERE division_id = ?";
            $participants = $this->model->query_bind($sql, [$division_id], 'array');
        
            $final_scores = [];
        
            foreach ($participants as $participant) {
                $participant_id = $participant['participant_id'];
        
                // Get top 2 wave scores from EACH heat
                $sql = "SELECT AVG(score) as avg_score FROM comp_judge_scores 
                        WHERE participant_id = ? 
                        GROUP BY heat_id, wave_number 
                        ORDER BY avg_score DESC 
                        LIMIT 4";  // 2 best waves per heat = 4 best scores total
        
                $top_scores = $this->model->query_bind($sql, [$participant_id], 'array');
        
                // Sum the best 4 scores for final ranking
                $total_score = array_sum(array_column($top_scores, 'avg_score'));
        
                $final_scores[] = [
                    'participant_id' => $participant_id,
                    'total_score' => $total_score
                ];
            }
        
            // Sort surfers by highest total_score
            usort($final_scores, function ($a, $b) {
                return $b['total_score'] <=> $a['total_score']; // Descending order
            });
        
            // Save standings in `comp_final_standings`
            foreach ($final_scores as $position => $data) {
                $rank = $position + 1; // 1st place, 2nd place, etc.
        
                $save_data = [
                    'division_id' => $division_id,
                    'participant_id' => $data['participant_id'],
                    'final_score' => $data['total_score'],
                    'rank' => $rank
                ];
        
                $this->model->insert($save_data, 'comp_final_standings');
            }
        
            return $final_scores;
        }

        function heat_scores() {
            $heat_id = segment(3) !== '' ? segment(3) : null;

            // Fetch heat info
            $heat_info = $this->model->get_where($heat_id, 'comp_heats');
        
            // Fetch participants with first name, last name, and jersey color
            $sql = "SELECT p.first_name, p.last_name, hp.jersey_color, hp.participant_id 
                    FROM comp_heat_participants hp 
                    JOIN comp_participants p ON hp.participant_id = p.id 
                    WHERE hp.heat_id = ?";
            $participants = $this->model->query_bind($sql, [$heat_id], 'array');

            // Fetch wave scores
            $sql = "SELECT participant_id, wave_number, avg_score 
                    FROM comp_wave_averages 
                    WHERE heat_id = ? 
                    GROUP BY participant_id, wave_number";
            $wave_scores = $this->model->query_bind($sql, [$heat_id], 'array');

            // Organize wave scores by participant_id
            $formatted_scores = [];
            foreach ($wave_scores as $score) {
                $formatted_scores[$score['participant_id']][] = [
                    'wave_number' => $score['wave_number'],
                    'avg_score' => $score['avg_score']
                ];
            }
        
            // Attach scores to participants
            foreach ($participants as &$participant) {
                $participant_id = $participant['participant_id'];
                $participant['scores'] = $formatted_scores[$participant_id] ?? [];
            }
        
            // Pass data to view
            $data = [
                'heat_info' => $heat_info,
                'participants' => $participants
            ];
        
            $this->view('heat_scores', $data);
        }

//-------------------------------------------------------------
//------------------   FINAL SCORES END  ----------------------
//-------------------------------------------------------------
        
        //-------------------------------------------------------------
        //------------------MAX 4 PARTICIPANTS-------------------------
        //-------------------------------------------------------------
        private function generate_four($comp_id, $participants, $total_participants, $division) {
            
            $jersey_colors = ["white", "red", "green", "blue"];
    
            for ($i = 0; $i < 2; $i++) {  // Loop for two heats
                $heat_data = [
                    'round' => 'Round 1',
                    'heat_number' => $i + 1, // Heat numbers start from 1
                    'comp_id' => $comp_id,
                    'division' => $division
                ];
                $heat_id = $this->model->insert($heat_data, 'comp_heats');

                // Assign the same participants to both heats
                for ($j = 0; $j < $total_participants; $j++) {

                    $participant_id = $participants[$j]['id'];

                    $color = $jersey_colors[$j % count($jersey_colors)]; // Assign jersey color

                    // Assign participant to the heat
                    $part_data = [
                        'heat_id' => $heat_id,
                        'participant_id' => $participant_id,
                        'jersey_color' => $color
                    ];

                    $heat_part_id = $this->model->insert($part_data, 'comp_heat_participants');

                }
            }
        }
    //-------------------------------------------------------------
    //------------------MAX 6 PARTICIPANTS-------------------------
    //-------------------------------------------------------------
        private function generate_six($comp_id, $participants, $total_participants, $division) {

            $jersey_colors = ["white", "red", "green", "blue"];
            
            for ($i = 0; $i < 2; $i++) { // Loop for two heats
                $heat_data = [
                    'round' => 'Round 1',
                    'heat_number' => $i + 1,
                    'comp_id' => $comp_id,
                    'division' => $division
                ];
                $heat_id = $this->model->insert($heat_data, 'comp_heats');
                
                // Assign up to 3 participants to this heat
                for ($j = 0; $j < 3 && ($i * 3 + $j) < $total_participants; $j++) {
                    
                    $participant_id = $participants[$i * 3 + $j]['id'];

                    $color = $jersey_colors[$j % count($jersey_colors)];

                    // Assign participant to the heat
                    $part_data = [
                        'heat_id' => $heat_id,
                        'participant_id' => $participant_id,
                        'jersey_color' => $color
                    ];
                    $heats_part_id = $this->model->insert($part_data, 'comp_heat_participants');

                }

            }
            //insert REPECHAGE heats to heats table
            $rep_heat_data = [
                'round' => 'Repechage 1',
                'heat_number' => 1,
                'comp_id' => $comp_id,
                'division' => $division
            ];
            $rep_heat_id = $this->model->insert($rep_heat_data, 'comp_heats');
            //insert FINAL to heats table
            $final_heat_data = [
                'round' => 'Final',
                'heat_number' => 1,
                'comp_id' => $comp_id,
                'division' => $division
            ];
            $final_heat_id = $this->model->insert($final_heat_data, 'comp_heats');
        }
    //-------------------------------------------------------------
    //------------------MAX 8 PARTICIPANTS-------------------------
    //-------------------------------------------------------------
        private function generate_eight($comp_id, $participants, $total_participants, $division) {
            
            $jersey_colors = ["white", "red", "green", "blue"];

            //------ Round 1 - 2 heats ----------------
            for ($i = 0; $i < 2; $i++) {
                $heat_data = [
                    'round' => 'Round 1',
                    'heat_number' => $i + 1,
                    'comp_id' => $comp_id,
                    'division' => $division
                ];
                $heat_id = $this->model->insert($heat_data, 'comp_heats');
                
                // Assign up to max 4 participants to this heat
                for ($j = 0; $j < 4 && ($i * 4 + $j) < $total_participants; $j++) {
                    
                    $participant_id = $participants[$i * 4 + $j]['id'];

                    $color = $jersey_colors[$j % count($jersey_colors)];

                    // Assign participant to the heat
                    $part_data = [
                        'heat_id' => $heat_id,
                        'participant_id' => $participant_id,
                        'jersey_color' => $color
                    ];

                    $heats_part_id = $this->model->insert($part_data, 'comp_heat_participants');
                }
            }

            //insert REPECHAGE heats to heats table
            $rep_heat_data = [
                'round' => 'Repechage 1',
                'heat_number' => 1,
                'comp_id' => $comp_id,
                'division' => $division
            ];
            $rep_heat_id = $this->model->insert($rep_heat_data, 'comp_heats');

            //insert Raund 2 to heats table
            $round_heat_data = [
                'round' => 'Round 2',
                'heat_number' => 1,
                'comp_id' => $comp_id,
                'division' => $division
            ];
            $r2_heat_id = $this->model->insert($round_heat_data, 'comp_heats');
            
            //insert Repechage 2 to heats table
            $rep2_heat_data = [
                'round' => 'Repechage 2',
                'heat_number' => 1,
                'comp_id' => $comp_id,
                'division' => $division
            ];
            $rep2_heat_id = $this->model->insert($rep2_heat_data, 'comp_heats');
           
            //insert FINAL to heats table
            $final_heat_data = [
                'round' => 'Final',
                'heat_number' => 1,
                'comp_id' => $comp_id,
                'division' => $division
            ];
            $final_heat_id = $this->model->insert($final_heat_data, 'comp_heats');
        }
    //-------------------------------------------------------------
    //------------------MAX 9 PARTICIPANTS-------------------------
    //-------------------------------------------------------------
        private function generate_nine($comp_id, $participants, $total_participants, $division) {
            
            $jersey_colors = ["white", "red", "green", "blue"];

            //------ Round 1 - 3 heats ----------------
            for ($i = 0; $i < 3; $i++) {
                $heat_data = [
                    'round' => 'Round 1',
                    'heat_number' => $i + 1,
                    'comp_id' => $comp_id,
                    'division' => $division
                ];
                $heat_id = $this->model->insert($heat_data, 'comp_heats');
                
                // Assign up to max 3 participants to this heat
                for ($j = 0; $j < 3 && ($i * 3 + $j) < $total_participants; $j++) {
                    
                    $participant_id = $participants[$i * 3 + $j]['id'];

                    $color = $jersey_colors[$j % count($jersey_colors)];

                    // Assign participant to the heat
                    $part_data = [
                        'heat_id' => $heat_id,
                        'participant_id' => $participant_id,
                        'jersey_color' => $color
                    ];

                    $heats_part_id = $this->model->insert($part_data, 'comp_heat_participants');
                }
            }

            //insert 2 REPECHAGE heats to heats table
            for ($i = 0; $i < 2; $i++) {
                $rep_heat_data = [
                    'round' => 'Repechage 1',
                    'heat_number' => $i + 1,
                    'comp_id' => $comp_id,
                    'division' => $division
                ];
                $rep_heat_id = $this->model->insert($rep_heat_data, 'comp_heats');
            }

            //insert Raund 2 to heats table
            $round_heat_data = [
                'round' => 'Round 2',
                'heat_number' => 1,
                'comp_id' => $comp_id,
                'division' => $division
            ];
            $r2_heat_id = $this->model->insert($round_heat_data, 'comp_heats');
            
            //insert Repechage 2 to heats table
            $rep2_heat_data = [
                'round' => 'Repechage 2',
                'heat_number' => 1,
                'comp_id' => $comp_id,
                'division' => $division
            ];
            $rep2_heat_id = $this->model->insert($rep2_heat_data, 'comp_heats');
           
            //insert FINAL to heats table
            $final_heat_data = [
                'round' => 'Final',
                'heat_number' => 1,
                'comp_id' => $comp_id,
                'division' => $division
            ];
            $final_heat_id = $this->model->insert($final_heat_data, 'comp_heats');
        }
    //-------------------------------------------------------------
    //------------------MAX 12 PARTICIPANTS------------------------
    //-------------------------------------------------------------
        private function generate_twelve($comp_id, $participants, $total_participants, $division) {
            
            $jersey_colors = ["white", "red", "green", "blue"];

            //------ Round 1 - 3 heats ----------------
            for ($i = 0; $i < 3; $i++) {
                $heat_data = [
                    'round' => 'Round 1',
                    'heat_number' => $i + 1,
                    'comp_id' => $comp_id,
                    'division' => $division
                ];
                $heat_id = $this->model->insert($heat_data, 'comp_heats');
                
                // Assign up to max 4 participants to this heat
                for ($j = 0; $j < 4 && ($i * 4 + $j) < $total_participants; $j++) {
                    
                    $participant_id = $participants[$i * 4 + $j]['id'];

                    $color = $jersey_colors[$j % count($jersey_colors)];

                    // Assign participant to the heat
                    $part_data = [
                        'heat_id' => $heat_id,
                        'participant_id' => $participant_id,
                        'jersey_color' => $color
                    ];

                    $heats_part_id = $this->model->insert($part_data, 'comp_heat_participants');
                }
            }

            //insert REPECHAGE heats to heats table
            for ($i = 0; $i < 2; $i++) {
                $rep_heat_data = [
                    'round' => 'Repechage 1',
                    'heat_number' => $i + 1,
                    'comp_id' => $comp_id,
                    'division' => $division
                ];
                $rep_heat_id = $this->model->insert($rep_heat_data, 'comp_heats');
            }

            //insert Raund 2 to heats table
            $round_heat_data = [
                'round' => 'Round 2',
                'heat_number' => 1,
                'comp_id' => $comp_id,
                'division' => $division
            ];
            $r2_heat_id = $this->model->insert($round_heat_data, 'comp_heats');
            
            //insert Repechage 2 to heats table
            $rep2_heat_data = [
                'round' => 'Repechage 2',
                'heat_number' => 1,
                'comp_id' => $comp_id,
                'division' => $division
            ];
            $rep2_heat_id = $this->model->insert($rep2_heat_data, 'comp_heats');
           
            //insert FINAL to heats table
            $final_heat_data = [
                'round' => 'Final',
                'heat_number' => 1,
                'comp_id' => $comp_id,
                'division' => $division
            ];
            $final_heat_id = $this->model->insert($final_heat_data, 'comp_heats');
        }
    //-------------------------------------------------------------
    //------------------MAX 16 PARTICIPANTS------------------------
    //-------------------------------------------------------------
        private function generate_sixteen($comp_id, $participants, $total_participants, $division) {
            
            $jersey_colors = ["white", "red", "green", "blue"];
            
            //------ Round 1 - 4 heats ----------------
            // Calculate number of heats (max 4 per heat, but spread evenly)
            $num_heats = 4;
            $heats = [];
            for ($i = 0; $i < $num_heats; $i++) {
                $heat_data = [
                    'round' => 'Round 1',
                    'heat_number' => $i + 1,
                    'comp_id' => $comp_id,
                    'division' => $division
                ];
                $heats[] = $this->model->insert($heat_data, 'comp_heats');
            }

            // Distribute participants round-robin
            foreach ($participants as $idx => $participant) {
                $heat_idx = $idx % $num_heats;
                $color = $jersey_colors[($idx / $num_heats) % count($jersey_colors)];
                $part_data = [
                    'heat_id' => $heats[$heat_idx],
                    'participant_id' => $participant['id'],
                    'jersey_color' => $color
                ];
                $this->model->insert($part_data, 'comp_heat_participants');
            }

            //insert REPECHAGE two heats to heats table
            for ($i = 0; $i < 2; $i++) {
                $rep_heat_data = [
                    'round' => 'Repechage 1',
                    'heat_number' => $i + 1,
                    'comp_id' => $comp_id,
                    'division' => $division
                ];
                $rep_heat_id = $this->model->insert($rep_heat_data, 'comp_heats');
            }

            //insert ROUND 2 two heats to heats table
            for ($i = 0; $i < 2; $i++) {
                $round_heat_data = [
                    'round' => 'Round 2',
                    'heat_number' => $i + 1,
                    'comp_id' => $comp_id,
                    'division' => $division
                ];
                $r2_heat_id = $this->model->insert($round_heat_data, 'comp_heats');
            }

            //insert REPECHAGE 2 two heats to heats table
            for ($i = 0; $i < 2; $i++) {
                $rep2_heat_data = [
                    'round' => 'Repechage 2',
                    'heat_number' => $i + 1,
                    'comp_id' => $comp_id,
                    'division' => $division
                ];
                $rep2_heat_id = $this->model->insert($rep2_heat_data, 'comp_heats');
            }

            //insert FINAL to heats table
            $final_heat_data = [
                'round' => 'Final',
                'heat_number' => 1,
                'comp_id' => $comp_id,
                'division' => $division
            ];
            $final_heat_id = $this->model->insert($final_heat_data, 'comp_heats');
        }

    }
?>