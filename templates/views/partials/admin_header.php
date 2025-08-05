<div id="header-sm">
    <div class="logo-pic">
        <img src="images/logo.png" alt="logo surf club">
    </div>
    <div class="logo">
        <a href="welcome/admin">ADMIN</a>
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
        <a href="welcome/admin">Admino Zona</a>
    </div>
    <div id="top-title" style="margin:auto"><?= $title ?></div>
    <div class="user text-right">
        <?= anchor('trongate_administrators/manage', '<i class="fa fa-user"></i>') ?>
        <?= anchor('trongate_administrators/logout', '<i class="fa fa-sign-out"></i>') ?>
    </div>
</div>