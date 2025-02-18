<div id="header-sm">
    <div id="slide-nav" class="test">
        <ul>
            <li><a href="pamokos/">Pamokos</a></li>
            <li><a href="nuoma/">Nuoma</a></li>
            <li><a href="stovykla/">Stovykla</a></li>
            <li><a href="stovyklos/suaugusiems/lanzarote">Kelionės</a></li>
            <li><a href="varzybos/">Varžybos</a></li>
            <li><a href="burelis/">Būrelis</a></li>
        </ul>
        <ul>
            <li><a href="skelbimai/">Skelbimai</a></li>
            <li><a href="test/port/">Orai</a></li>
            <li><a href="webcamai/">Webcamai</a></li>
            <li><a href="straipsniai/">Straipsniai</a></li>
            <li><a href="#">Komanda</a></li>
            <li><a href="kontaktai/">Kontaktai</a></li>
        </ul>
        <ul>
            <li><a href="karjera/">Karjera</a></li>
            <li><a href="parama/">Parama</a></li>
            <li><a href="#">Nuorodos</a></li>
        </ul>
        <ul>
            <li><a href="#">Ataskaitos</a></li>
            <li><a href="members/login/">Login</a></li>
        </ul>
    </div>
    <div class="logo-pic">
        <img src="images/logo.png" alt="logo surf club">
    </div>
    <div class="logo">
        <?= anchor(BASE_URL, 'Molas Surf Club') ?>
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
            <li><a href="pamokos">Pamokos</a></li>
            <li><a href="nuoma">Nuoma</a></li>
            <li><a href="stovykla">Stovykla</a></li>
            <li><a href="varzybos">Varžybos</a></li>
            <li><a href="kontaktai">Kontaktai</a></li>
        </ul>
    </div>
    <div id="megaburger" class="burger col-3" onclick="toggleMegaNav()">
        <div class="line1"></div>
        <div class="line2"></div>
        <div class="line3"></div>
    </div>
</div>