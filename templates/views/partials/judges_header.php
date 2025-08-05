<?php $user_info = Modules::run("competitions/_get_judge_info") ?>
<div id="header-sm">
    <div class="logo-pic">
        <img src="images/logo.png" alt="logo surf club">
    </div>
    <div class="logo">
        <a href="competitions">Judges<br><h6 style="padding: 0; margin: -5px; font-size: 0.5rem;">ver. 1.05</h6></a>
    </div>
    <div id="hamburger" class="burger col-3" onclick="toggleSlideNav()">
        <div class="line1"></div>
        <div class="line2"></div>
        <div class="line3"></div>
    </div>
</div>

<div id="header-lg">
    <div class="logo-pic">
        <img src="images/logo.png" alt="logo surf club">
    </div>
    <div class="logo">
        <a href="competitions">Judge's Area<br><h6 class="" style="padding: 0; margin: -5px;text-transform:lowercase;font-size: 0.5rem;">ver 1.05</h6></a>
    </div>
    <div>
        <ul id="top-nav">
            <li><a style="text-transform:uppercase;" href="competitions"><?= $user_info->role ?> Dashboard</a></li>
        </ul>
    </div>
    <div class="user">
        <?php if ($user_info->organization) { ?>
            <p style="color: orangered;">- <?= $user_info->organization ?> -</p>
        <?php } else if ($user_info->role === 'admin') {?>
        <p style="color: lawngreen;">== <?= $user_info->name ?> ==</p>
        <?php } else {?>
            <p >= <?= $user_info->name ?> =</p>
        <?php } ?>
        <?= anchor('competitions/judge_profile', '<i class="fa fa-user"></i>') ?>
        <?= anchor('competitions/logout', '<i class="fa fa-sign-out"></i>') ?>
    </div>
</div>