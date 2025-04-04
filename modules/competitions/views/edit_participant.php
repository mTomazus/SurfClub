<h2>Edit participant</h2>
<div id="response"></div>
<form mx-post="competitions/submit_create_participant/<?= $id ?>" mx-target="#response">
<?php
    echo form_input('first_name', $first_name);
    echo form_input('last_name', $last_name);
    echo form_input('email', $email);

    $options = array(); // Initialize empty array
    foreach ($rows as $row) { 
        $options[$row->id] = $row->name . ' ' . $row->year;
    }
    echo form_dropdown('comp_id', $options);

    $genders = ['Male' => 'Male', 'Female' => 'Female'];
    echo form_dropdown('gender', $genders);
    $groups = ['U12' => 'Under 12', 'U15' => 'Under 15', 'U18' => 'Under 18', 'ADT' => 'Adult', 'VET' => 'Veteran'];
    echo form_dropdown('age_group', $groups);
?>
    <div class="modal-footer">
        <button type="submit" class="modal-submit" name="update">update</button>
        <button class="close" onclick="closeModal()">Cancel</button>
        <button class="modal-delete" mx-delete="competitions/submit_delete_participant/<?= $id ?>">delete</button>
    </div>
<?php
    echo form_close();
?>
<style>
    h3 {
        padding: 0.5rem;
    }
    #response {
        background: lawngreen;
        margin: 1rem;
        color: black;
    }
    .modal-footer {
        grid-column: 1 / span 2;
        background: none;
        border: none;
        justify-content: space-evenly;
    }
    .close {
        background: none;
        border: 2px solid skyblue;
        box-shadow: 0 0 10px skyblue;
        color: skyblue;
        font-family:inherit;
    }
    .modal-delete:hover {
        background:red;
        border:2px solid white;
        color:white;
        transition: all 0.5s;
    }
    .close:hover {
        background:skyblue;
        border:2px solid white;
        color:black;
        transition: all 0.5s;
    }
    .modal-submit:hover {
        background:white;
        border:2px solid black;
        color:black;
        transition: all 0.5s;
    }
</style>