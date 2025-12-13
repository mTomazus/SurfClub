<?php $user_info = Modules::run("competitions-users/_get_user_info") ?>

<div class="logo-pic" style="display: flex;align-items: center;text-align: center;">
    <img src="competitions-users_module/images/logo-comp-white.png" alt="logo surf club">
    <a class="" href="competitions">Your Surf Panel<br><h6 class="" style="padding: 0; margin: -5px;text-transform:lowercase;font-size: 0.5rem;">ver 1.05</h6></a>
</div>
<div>
    <ul id="top-nav">
        <li><a style="text-transform:uppercase;" href="competitions-users">User's Dashboard</a></li>
    </ul>
</div>
<div class="user">
    <p >= <?= out($user_info[0]['name']) ?> =</p>
    <a mx-get="competitions-users/profile_info" mx-build-modal="user-info-modal"><i class="fa fa-user"></i></a>
    <?= anchor('competitions-users/logout', '<i class="fa fa-sign-out"></i>') ?>
</div>