<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= BASE_URL ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/trongate.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/trongate-datetime.css">
    <link rel="stylesheet" href="<?= THEME_DIR ?>css/admin-theme.css">
    <?= $additional_includes_top ?>
    <title>Admin</title>
</head>
<body>
<header>
    <div class="logo-pic hide-sm">
        <img src="images/logo.png" alt="logo surf club">
    </div>
    <div class="hide-sm logo">
        <h2>Admin</h2>
    </div>
    <nav class="hide-sm">
        <ul>
            <li><?= anchor(BASE_URL, 'Homepage', array('target' => '_blank')) ?></li>
            <li><?= anchor('https://trongate.io/docs', 'Docs', array('target' => '_blank')) ?></li>
            <li><?= anchor('https://trongate.io/help_bar', 'Help Bar', array('target' => '_blank')) ?></li>
            <li><?= anchor('https://trongate.io/learning-zone', 'Learning Zone', array('target' => '_blank')) ?></li>
            <li><?= anchor('https://trongate.io/news', 'News', array('target' => '_blank')) ?></li>
            <li><?= anchor('https://trongate.io/module_requests/browse', 'Module Requests', array('target' => '_blank')) ?></li>
            <li><?= anchor('https://trongate.io/module-market', 'Module Market', array('target' => '_blank')) ?></li>
        </ul>
    </nav>
    <div id="hamburger" class="burger hide-lg" onclick="openSlideNav()">
        <div class="line1"></div>
        <div class="line2"></div>
        <div class="line3"></div>
    </div>
    <div class="hide-lg logo">
        <h2>Admin</h2>
    </div>
    <div class="logo-pic hide-lg">
        <img src="images/logo.png" alt="logo surf club">
    </div>
    <div>
        <?= anchor('trongate_administrators/manage', '<i class="fa fa-gears"></i>') ?>
        <?= anchor('trongate_administrators/account', '<i class="fa fa-user"></i>') ?>
        <?= anchor('trongate_administrators/logout', '<i class="fa fa-sign-out"></i>') ?>
    </div>
</header>
<div class="wrapper">
    <div id="sidebar">
        <h3>Menu</h3>
        <nav id="left-nav">
            <?= Template::partial('partials/admin/dynamic_nav') ?>
        </nav>
    </div>
    <div>
        <main>
            <?= Template::display($data) ?>
        </main>
    </div>
</div>
<div id="slide-nav">
    <div id="hamburger" class="burger show-burger" onclick="closeSlideNav()">
        <div class="line1"></div>
        <div class="line2"></div>
        <div class="line3"></div>
    </div>
    <ul auto-populate="true"></ul>
</div>

<script src="js/admin.js"></script>
<script src="js/trongate-datetime.js"></script>
<?= $additional_includes_btm ?>

</body>
</html>