<div id="header-sm">
    
    <div id="slide-nav" onclick="toggleSlideNav()" class="slide justify-content-around" mx-get="competitions/admin_dash" mx-select=".side-nav" mx-trigger="load" mx-target="#result">
        <div class="slide-header">
            <div class="slide-footer">
                <?= anchor('competitions/judge_dash', '<i class="fa fa-user"></i>') ?>
                <div class="slide_user">
                    <h4><?= $user->name ?></h4>
                    <h3><?= $user->role ?></h3>
                </div>
                <?= anchor('competitions/logout', '<i class="fa fa-sign-out"></i>') ?>
            </div>
        </div>
        <div id="result"></div>
    </div>
    <div class="logo-pic">
        <img src="images/logo.png" alt="logo surf club">
    </div>
    <div class="logo">
        <a href="competitions/judge_dash">Judges</a>
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
        <a href="competitions/judge_dash">Judge's Area</a>
    </div>
    <div>
        <ul id="top-nav">
            <li><a style="text-transform:uppercase;" href="competitions/<?= $user->role ?>_dash"><?= $user->role ?> Dashboard</a></li>
        </ul>
    </div>
    <div class="user">
        <p>== <?= $user->name ?> ==</p>
        <?= anchor('competitions/judge_dash', '<i class="fa fa-user"></i>') ?>
        <?= anchor('competitions/logout', '<i class="fa fa-sign-out"></i>') ?>
    </div>
</div>