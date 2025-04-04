
    <div class="side-nav">
        <a class="side_button" mx-get="competitions/score_heat" mx-target="#form-container" mx-select="form"><i class="fa fa-trophy" aria-hidden="true"></i><h4>Judging Heats</h4></a>
        <a class="side_button" mx-get="competitions/create_comp" mx-target="#form-container" mx-select="#form-table"><i class="fa fa-plus-square" aria-hidden="true"></i><h4>Add Competitions</h4></a>
        <a class="side_button" mx-get="competitions-heats/heat_generation_page" mx-target="#form-container" mx-select="#form-container"><i class="fa fa-database" aria-hidden="true"></i><h4>Generate Heats</h4></a>
        <a class="side_button" mx-get="competitions-heats/show_heats/1" mx-target="#form-container" mx-select="#just-heats"><i class="fa fa-id-card-o" aria-hidden="true"></i><h4>Show Heats</h4></a>
        <a class="side_button" mx-get="competitions/create_participant" mx-target="#form-container" mx-select="#form-table"><i class="fa fa-users" aria-hidden="true"></i><h4>Register Participant</h4></a>
        <a class="side_button" mx-get="competitions/show_participants" mx-target="#form-container" mx-select="#participantsTable"><i class="fa fa-address-book-o" aria-hidden="true"></i><h4>Show Participants</h4></a>
        <a class="side_button" mx-get="competitions-heats/heat_schedule_page" mx-target="#form-container" mx-select="#just-heats"><i class="fa fa-hourglass-start" aria-hidden="true"></i><h4>Schedule Heats</h4></a>
        <a class="side_button" mx-get="competitions/judge_scores" mx-target="#form-container"><i class="fa fa-tasks" aria-hidden="true"></i><h4>Mine Scores</h4></a>
        <a class="side_button" mx-get="competitions/create_judge" mx-target="#form-container" mx-select="#form-table"><i class="fa fa-bullhorn" aria-hidden="true"></i><h4>Register Judges</h4></a>
    </div>
    <div id="form-container"></div>
    <div id="load-on" mx-get="competitions/score_heat" mx-target="#form-container" mx-select="form" mx-trigger="load"></div>