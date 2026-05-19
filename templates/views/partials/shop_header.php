<div id="header-sm">
    <div id="slide-nav" class="test">
        <ul>
            <li><a href="pamokos/">Pamokos</a></li>
            <li><a href="nuoma/">Nuoma</a></li>
            <li><a href="stovykla/">Stovykla</a></li>
            <li><a href="varzybos/">Varžybos</a></li>
            <li><a href="news/">Naujienos</a></li>
        </ul>
        <ul>
            <li><a href="competitions-heats/">LiveHeats</a></li>
            <li><a href="test/port/">Orai</a></li>
            <li><a href="competitions/">Teisėjams</a></li>
            <li><a href="kontaktai/">Kontaktai</a></li>
            <li><a href="parama/">Parama</a></li>
        </ul>
    </div>
    <div class="logo-pic">
        <img src="images/logo-shop-1.webp" alt="logo surf club">
    </div>
    <div class="logo">
        <?= anchor(BASE_URL, 'Molas Surf Shop') ?>
    </div>
    <div id="hamburger" class="burger col-3" onclick="toggleSlideNav()">
        <div class="line1"></div>
        <div class="line2"></div>
        <div class="line3"></div>
    </div>
</div>
<div id="header-lg">
    <div class="logo-pic">
        <img src="images/logo-shop-1.webp" alt="logo surf club">
    </div>
    <div class="logo">
        <?= anchor(BASE_URL,  'Molas Surf Shop') ?>
    </div>
    <div id="nav">
        <ul id="top-nav">
            <li><a href="products/category/new">NEW</a></li>
            <li><a href="products/category/best">BEST</a></li>
            <li><a href="products/category/surf">SURF</a></li>
            <li><a href="products/category/beach">BEACH</a></li>
        </ul>
    </div>
    <div class="mr-1">
        <a href="products/login" aria-label="log in"><i class="fa fa-user"></i></a>
        <a href="products/wishlist" aria-label="wish list"><i class="fa fa-heart"></i></a>
        <button onclick="openCartDrawer()" class="cart-icon-btn" aria-label="shopping cart">
            <i class="fa fa-shopping-basket">
                <?php if (!empty($_SESSION['cart'])): ?>
                    <span class="cart-count"><?= array_sum($_SESSION['cart']) ?></span>
                <?php endif; ?>
            </i>
        </button>
    </div>
    <div id="megaburger" class="burger col-3" onclick="toggleMegaNav()">
        <div class="line1"></div>
        <div class="line2"></div>
        <div class="line3"></div>
    </div>
</div>
<style>
    .fa-shopping-basket {
        position:relative;
    }
    .cart-count {
        position: absolute;
        background: white;
        padding-inline: 0.2rem;
        border-radius: 10px;
        right: -4px;
        bottom: -5px;
        /* border: 1px solid; */
        font-size: 11px;
    }
</style>