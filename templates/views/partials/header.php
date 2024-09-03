<div id="header-sm">
    <div id="slide-nav">
        <ul auto-populate="true"></ul>
        <div>
            <?= anchor('account', '<i class="fa fa-user"></i>') ?>
            <?= anchor('logout', '<i class="fa fa-sign-out"></i>') ?>
        </div>
    </div>
    <div class="logo-pic">
        <img src="images/logo.png" alt="logo surf club">
    </div>
    <div class="logo">
        <?= anchor(BASE_URL, WEBSITE_NAME) ?>
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
        <?= anchor(BASE_URL, WEBSITE_NAME) ?>
    </div>
    <div>
        <ul id="top-nav">
            <li><a href="pamokos/">Pamokos</a></li>
            <li><a href="/nuoma/">Nuoma</a></li>
            <li><a href="/stovyklos/">Stovyklos</a></li>
            <li><a href="/varzybos/">Varžybos</a></li>
            <li><a href="/burelis/">Būrelis</a></li>
            <li><a href="/straipsniai/">Straipsniai</a></li>
            <li><a href="/contacts/">Kontaktai</a></li>
        </ul>
    </div>
    <div id="megaburger" class="burger col-3" onclick="toggleMegaNav()">
        <div class="line1"></div>
        <div class="line2"></div>
        <div class="line3"></div>
    </div>
</div>