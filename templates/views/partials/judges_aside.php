<?php 
$user_info = Modules::run("competitions/_get_judge_info");
if ($user_info->role === 'admin') { ?>
    
    <div class="side-nav">
        <a class="side-button" onclick="toggleSlideNav()" mx-get="competitions/score_heat" mx-target="#form-container" mx-select="#form-container"><i class="fa fa-trophy" aria-hidden="true"></i><h4>Live Judging</h4></a>
        <a class="side-button" onclick="toggleSlideNav()" mx-get="competitions/judge_scores" mx-target="#form-container" mx-select="#form-container"><i class="fa fa-tasks" aria-hidden="true"></i><h4>My Scores</h4></a>
        <a class="side-button" onclick="toggleSlideNav()" mx-get="competitions/edit_scores" mx-target="#form-container" mx-select="#form-container"><i class="fa fa-edit" aria-hidden="true"></i><h4>Edit Scores</h4></a>
        <li class=""><a class="side-button" onclick=toggleSubMenu(this)><i class="fa fa-id-card-o" aria-hidden="true"></i><h4>Show Heats</h4><i class="fa fa-chevron-down" aria-hidden="true"></i></a>
            <ul class="sub-menu">
                <?php
                $comps = Modules::run("competitions/get_competitions");
                if (!empty($comps)) {
                    foreach ($comps as $comp) {
                        if ($comp->status === 'generated' || $comp->status === 'running') {
                            echo '<li class=""><a class="side-button" onclick="toggleSlideNav()" mx-get="competitions-heats/show_heats/' . $comp->id . '" mx-target="#form-container" mx-select="#just-heats"><h4 style="text-align:center">' . $comp->name . ' ' . $comp->year . '</h4></a></li>';
                        }
                    }
                }
                ?>
            </ul>
        </li>
        <a class="side-button" onclick="toggleSlideNav()" mx-get="competitions-heats/heat_schedule_page" mx-target="#form-container" mx-select="#heat-schedule"><i class="fa fa-hourglass-start" aria-hidden="true"></i><h4>Schedule Heats</h4></a>
        <a class="side-button" onclick="toggleSlideNav()" mx-get="competitions/create_participant" mx-target="#form-container" mx-select="#form-table"><i class="fa fa-book" aria-hidden="true"></i><h4>Register Participant</h4></a>
        <a class="side-button" onclick="toggleSlideNav()" mx-get="competitions/show_participants/" mx-target="#form-container" mx-select="#participantsTable"><i class="fa fa-address-book-o" aria-hidden="true"></i><h4>Participants</h4></a>
    </div>

<?php } else if ($user_info->role === 'organizer') { ?>

    <div class="side-nav">
        <li class=""><a class="side-button" onclick=toggleSubMenu(this)><i class="fa fa-futbol-o" aria-hidden="true"></i><h4>Competitions</h4><i class="fa fa-chevron-down" aria-hidden="true"></i></a>
            <ul class="sub-menu">
                <li class="">
                    <a class="side-button" onclick="toggleSlideNav()" mx-get="competitions/create_comp" mx-target="#form-container" mx-select="#form-table"><i class="fa fa-plus-square" aria-hidden="true"></i><h4>Create</h4></a>
                </li>
                <li class="">
                    <a class="side-button" onclick="toggleSlideNav()" mx-get="competitions/create_comp" mx-target="#form-container" mx-select="#comp-table"><i class="fa fa-magic" aria-hidden="true"></i><h4>Show / Edit</h4></a>
                </li>
            </ul>
        </li>
                <a class="side-button" onclick="toggleSlideNav()" mx-get="competitions-heats/heat_schedule_page" mx-target="#form-container" mx-select="#heat-schedule"><i class="fa fa-hourglass-start" aria-hidden="true"></i><h4>Schedule Heats</h4></a>
        <li class=""><a class="side-button" onclick=toggleSubMenu(this)><i class="fa fa-id-card-o" aria-hidden="true"></i><h4>Show Heats</h4><i class="fa fa-chevron-down" aria-hidden="true"></i></a>
            <ul class="sub-menu">
                <?php
                $comps = Modules::run("competitions/get_competitions");
                if (!empty($comps)) {
                    foreach ($comps as $comp) {
                        if ($comp->status === 'generated' || $comp->status === 'running') {
                            echo '<li class=""><a class="side-button" onclick="toggleSlideNav()" mx-get="competitions-heats/show_heats/' . $comp->id . '" mx-target="#form-container" mx-select="#just-heats"><h4 style="text-align:center">' . $comp->name . ' ' . $comp->year . '</h4></a></li>';
                        }
                    }
                }
                ?></ul>
        </li>
        <a class="side-button" onclick="toggleSlideNav()" mx-get="competitions/create_judge" mx-target="#form-container" mx-select="#form-table"><i class="fa fa-user-plus" aria-hidden="true"></i><h4>Register Judges</h4></a>
        <a class="side-button" onclick="toggleSlideNav()" mx-get="competitions/create_judge" mx-target="#form-container" mx-select="#judge-table"><i class="fa fa-users" aria-hidden="true"></i><h4>Show Judges</h4></a>
    </div>

<?php } else { ?>

    <div class="side-nav">
        <a class="side-button" onclick="toggleSlideNav()" mx-get="competitions/score_heat" mx-target="#form-container" mx-select="#form-container"><i class="fa fa-trophy" aria-hidden="true"></i><h4>Live Judging</h4></a>
        <a class="side-button" onclick="toggleSlideNav()" mx-get="competitions/judge_scores" mx-target="#form-container" mx-select="#form-container"><i class="fa fa-tasks" aria-hidden="true"></i><h4>My Scores</h4></a>
        <li class=""><a class="side-button" onclick=toggleSubMenu(this)><i class="fa fa-id-card-o" aria-hidden="true"></i><h4>Show Heats</h4><i class="fa fa-chevron-down" aria-hidden="true"></i></a>
            <ul class="sub-menu">
                <?php
                $comps = Modules::run("competitions/get_competitions");
                if (!empty($comps)) {
                    foreach ($comps as $comp) {
                        if ($comp->status === 'generated' || $comp->status === 'running') {
                            echo '<li class=""><a class="side-button" onclick="toggleSlideNav()" mx-get="competitions-heats/show_heats/' . $comp->id . '" mx-target="#form-container" mx-select="#just-heats"><h4 style="text-align:center">' . $comp->name . ' ' . $comp->year . '</h4></a></li>';
                        }
                    }
                }
                ?></ul>
        </li>
        <a class="side-button" onclick="toggleSlideNav()" mx-get="competitions/show_participants" mx-target="#form-container" mx-select="#participantsTable"><i class="fa fa-address-book-o" aria-hidden="true"></i><h4>All Participants</h4></a>
    </div>

<?php } ?>