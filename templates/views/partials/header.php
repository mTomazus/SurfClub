<div id="header-sm">
    <div id="slide-nav">
        <div class="slide-nav-brand">
            <img src="images/logo.png" alt="logo surf club" width="30" height="30" loading="lazy" decoding="async">
            <span>Molas Surf Club</span>
        </div>
        <nav class="slide-nav-links">
            <div class="slide-nav-section">
                <span class="slide-nav-section-title">Veikla</span>
                <a href="pamokos/" style="--i:0">Pamokos</a>
                <a href="nuoma/" style="--i:1">Nuoma</a>
                <a href="stovykla/" style="--i:2">Stovykla</a>
                <a href="renginiai/" style="--i:3">Renginiai</a>
            </div>
            <div class="slide-nav-section">
                <span class="slide-nav-section-title">Naujienos</span>
                <a href="news/" style="--i:4">Naujienos</a>
                <a href="products/" style="--i:5">Parduotuvė</a>
                <a href="test/port/" style="--i:6">Orai</a>
            </div>
            <div class="slide-nav-section">
                <span class="slide-nav-section-title">Apie mus</span>
                <a href="kontaktai/" style="--i:7">Kontaktai</a>
                <a href="komanda/" style="--i:9">Komanda</a>
                <a href="parama/" style="--i:10">Parama</a>
            </div>
        </nav>
        <div class="slide-nav-footer">
            <a href="https://www.instagram.com/banglente/" target="_blank" rel="noopener">
                <img src="images/in-icon.png" alt="instagram" width="22" height="22" loading="lazy" decoding="async">
            </a>
            <a href="https://www.facebook.com/banglente" target="_blank" rel="noopener">
                <img src="images/fb-icon.png" alt="facebook" width="22" height="22" loading="lazy" decoding="async">
            </a>
            <a href="https://www.youtube.com/channel/UCbcobM7kKzfznOQEpIrZJqA" target="_blank" rel="noopener">
                <img src="images/yt-icon.png" alt="youtube" width="22" height="22" loading="lazy" decoding="async">
            </a>
            <a href="https://t.me/LT_Serfing" target="_blank" rel="noopener">
                <img src="images/tg-icon.png" alt="telegram" width="22" height="22" loading="lazy" decoding="async">
            </a>
        </div>
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
        <?= anchor(BASE_URL, 'Molas Surf Club') ?>
    </div>
    <div>
        <ul id="top-nav">
            <li><a href="pamokos">Pamokos</a></li>
            <li><a href="nuoma">Nuoma</a></li>
            <li><a href="stovykla">Stovykla</a></li>
            <li><a href="renginiai">Renginiai</a></li>
            <li><a href="kontaktai">Kontaktai</a></li>
        </ul>
    </div>
    <div id="megaburger" class="burger col-3" onclick="toggleMegaNav()">
        <div class="line1"></div>
        <div class="line2"></div>
        <div class="line3"></div>
    </div>
</div>