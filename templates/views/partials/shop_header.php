<?php
/**
 * Shop header — Molas Surf Shop.
 *
 * Two responsive bars share the hooks app.js expects:
 *   #header-sm (<860px) … #hamburger → toggleSlideNav() → #slide-nav.isShown
 *   #header-lg (>=860px) … #megaburger → toggleMegaNav() → .meganav-links
 * The cart button (openCartDrawer) + .cart-count badge live in both bars.
 * Styling is scoped here and built on the existing shop tokens
 * (steel-blue / floralwhite / Baskervville) defined in shop.css.
 */
$cart_count = !empty($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<div id="header-sm">
    <img class="sh-brand__mark" src="images/logo-shop-1.webp" alt="" width="34" height="34" decoding="async" style="z-index:1;">
    
    <a class="sh-brand" href="<?= BASE_URL ?>" aria-label="Molas Surf Shop" style="z-index:1;">
        <span class="sh-brand__word">Molas Surf Shop</span>
    </a>

    <div id="hamburger" class="burger" onclick="toggleSlideNav()" aria-label="Atidaryti meniu" style="z-index:1;">
        <div class="line1"></div>
        <div class="line2"></div>
        <div class="line3"></div>
    </div>

</div>

<nav id="slide-nav" aria-label="Pagrindinis meniu" style="z-index:-1;">
    <div class="sh-slide__inner reveal">
        <p class="sh-slide__eyebrow" style="--i:0">Parduotuvė</p>
        <ul class="sh-slide__shop" style="--i:1">
            <li><a href="products/category/new">NEW</a></li>
            <li><a href="products/category/best">BEST</a></li>
            <li><a href="products/category/surf">SURF</a></li>
            <li><a href="products/category/beach">BEACH</a></li>
        </ul>

        <p class="sh-slide__eyebrow" style="--i:2">Klubas</p>
        <ul class="sh-slide__main" style="--i:3">
            <li><a href="pamokos/">Pamokos</a></li>
            <li><a href="nuoma/">Nuoma</a></li>
            <li><a href="stovykla/">Stovykla</a></li>
            <li><a href="varzybos/">Varžybos</a></li>
            <li><a href="news/">Naujienos</a></li>
            <li><a href="test/port/">Orai</a></li>
            <li><a href="komanda/">Komanda</a></li>
            <li><a href="kontaktai/">Kontaktai</a></li>
        </ul>

        <div class="sh-slide__actions" style="--i:4">
            <a href="products/login"><i class="fa fa-user" aria-hidden="true"></i> Prisijungti</a>
            <a href="products/wishlist"><i class="fa fa-heart" aria-hidden="true"></i> Pageidavimai</a>
        </div>
    </div>
</nav>

<div id="header-lg">
    <a class="sh-brand" href="<?= BASE_URL ?>" aria-label="Molas Surf Shop">
        <img class="sh-brand__mark" src="images/logo-shop-1.webp" alt="" width="38" height="38" decoding="async">
        <span class="sh-brand__word">Molas Surf Shop</span>
    </a>

    <nav id="nav" aria-label="Parduotuvės kategorijos">
        <ul id="top-nav">
            <li><a href="products/category/new">NEW</a></li>
            <li><a href="products/category/best">BEST</a></li>
            <li><a href="products/category/surf">SURF</a></li>
            <li><a href="products/category/beach">BEACH</a></li>
        </ul>
    </nav>

    <div class="sh-actions">
        <a class="sh-icon" href="products/login" aria-label="Prisijungti"><i class="fa fa-user" aria-hidden="true"></i></a>
        <a class="sh-icon" href="products/wishlist" aria-label="Pageidavimų sąrašas"><i class="fa fa-heart" aria-hidden="true"></i></a>
        <button class="sh-icon sh-cart" type="button" aria-label="Krepšelis" onclick="openCartDrawer()">
            <i class="fa fa-shopping-basket" aria-hidden="true"></i>
            <?php if ($cart_count): ?><span class="cart-count"><?= $cart_count ?></span><?php endif; ?>
        </button>
        <div id="megaburger" class="burger" onclick="toggleMegaNav()" aria-label="Atidaryti navigaciją">
            <div class="line1"></div>
            <div class="line2"></div>
            <div class="line3"></div>
        </div>
    </div>
</div>

<style>
/* ============================================================
   Molas Surf Shop — header redesign (sticky glass bar + slide
   menu). Built on the existing shop tokens. JS hooks preserved:
   #slide-nav/.isShown, #hamburger, #megaburger, .line1-3,
   .cart-count, openCartDrawer(), toggleSlideNav/MegaNav().
   ============================================================ */

/* ── Brand / wordmark ───────────────────────────────────── */
.sh-brand {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    text-decoration: none;
    color: var(--primary-darker);
    white-space: nowrap;
}
.sh-brand__mark { display: block; height: 34px; width: auto; margin-left: 0.5rem;}
#header-lg .sh-brand__mark { height: 38px; }
.sh-brand__word {
    font-family: "Baskervville", "Didot", serif;
    font-size: clamp(1.1rem, 2.4vw, 1.6rem);
    font-weight: 700;
    letter-spacing: 0.01em;
    line-height: 1;
}

/* ── Desktop category nav ───────────────────────────────── */
#header-lg #nav { margin: 0; }
#top-nav {
    display: flex;
    align-items: center;
    gap: clamp(1rem, 2.5vw, 2.25rem);
    margin: 0;
    padding: 0;
    list-style: none;
}
#top-nav li { margin: 0; }
#top-nav a {
    position: relative;
    color: var(--primary-darker);
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    padding-block: 0.35rem;
    transition: color var(--dur-micro) var(--ease-out);
}
#top-nav a::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 2px;
    border-radius: 2px;
    background: var(--primary);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform var(--dur-std) var(--ease-out);
}
#top-nav a:hover { color: var(--primary); }
#top-nav a:hover::after,
#top-nav a:focus-visible::after { transform: scaleX(1); }

/* ── Action icons ───────────────────────────────────────── */
.sh-actions { display: flex; align-items: center; gap: 0.25rem; }
.sh-icon {
    position: relative;
    display: inline-grid;
    place-items: center;
    width: 40px;
    height: 40px;
    border: 0;
    border-radius: 50%;
    background: transparent;
    color: var(--primary-darker);
    text-decoration: none;
    cursor: pointer;
    transition: background var(--dur-micro) var(--ease-out),
                color var(--dur-micro) var(--ease-out),
                transform var(--dur-micro) var(--ease-out);
}
.sh-icon .fa { margin: 0; font-size: 1.15rem; }
.sh-icon:hover { background: hsl(210 44% 29% / 0.08); color: var(--primary); }
.sh-icon:active { transform: scale(0.92); }
.sh-icon:focus-visible { outline: 2px solid var(--primary); outline-offset: 2px; }

/* Cart count badge */
.sh-cart .cart-count {
    position: absolute;
    top: 2px;
    right: 1px;
    min-width: 17px;
    height: 17px;
    padding: 0 4px;
    display: grid;
    place-items: center;
    border-radius: 999px;
    background: var(--primary);
    color: #fff;
    font-family: system-ui, sans-serif;
    font-size: 0.66rem;
    font-weight: 700;
    line-height: 1;
    box-shadow: 0 0 0 2px var(--secondary-color);
}

#header-sm {
    background: transparent;
    -webkit-backdrop-filter: blur(10px);
    backdrop-filter: blur(10px);
}

/* ── Slide menu (full-screen, frosted) ──────────────────── */
#slide-nav {
    position: fixed;
    top: 0;
    width: 100%;
    min-height: 100dvh;
    margin: 0;
    padding: 0;
    display: block;
    background: hsl(38 100% 97% / 0.97);
    transform: translateX(-105%);
    transition: transform var(--dur-emphasis) var(--ease-in-out);
    z-index: 40;
    overflow-y: auto;
    overscroll-behavior: contain;
    box-shadow: none;
    -webkit-box-shadow: none;
}
#slide-nav::-webkit-scrollbar { width: 0rem; }
#slide-nav::-webkit-scrollbar-track { background: hsl(38 100% 97% / 0.97); }
#slide-nav::-webkit-scrollbar-thumb {
    background: hsl(210 44% 29% / 0.15);
    border-radius: 999px;
}
#slide-nav.isShown { transform: translateX(0); }
.sh-slide__inner {
    box-sizing: border-box;
    min-height: 100%;
    padding: calc(65px + 1.5rem) clamp(1.5rem, 8vw, 3rem) 2.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    text-align: left;
}
.sh-slide__inner ul { list-style: none; margin: 0; padding: 0; }
.sh-slide__eyebrow {
    margin: 1.4rem 0 0.5rem;
    font-family: system-ui, sans-serif;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.22em;
    color: var(--primary);
    opacity: 0.85;
}
#slide-nav a {
    color: var(--primary-darker);
    text-decoration: none;
    transition: color var(--dur-micro) var(--ease-out),
                padding-left var(--dur-std) var(--ease-out);
}
#slide-nav a:hover,
#slide-nav a:focus-visible { color: var(--primary); }
.sh-slide__shop { display: flex; flex-wrap: wrap; gap: 0.4rem 1.5rem; }
.sh-slide__shop a {
    font-family: "Baskervville", "Didot", serif;
    font-size: clamp(1.6rem, 8vw, 2.4rem);
    font-weight: 700;
    letter-spacing: 0.02em;
}
.sh-slide__main li { border-bottom: 1px solid hsl(210 44% 29% / 0.10); }
.sh-slide__main a {
    display: block;
    padding: 0.7rem 0;
    font-family: "Baskervville", "Didot", serif;
    font-size: 1.35rem;
}
.sh-slide__main a:hover { padding-left: 0.5rem; }
.sh-slide__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem 1.5rem;
    margin-top: 1.75rem;
    padding-top: 1.25rem;
    border-top: 1px solid hsl(210 44% 29% / 0.12);
}
.sh-slide__actions a {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}
.sh-slide__actions .fa { color: var(--primary); }

/* Staggered reveal — replays each time the menu opens */
#slide-nav.isShown .reveal > * {
    opacity: 0;
    transform: translateY(10px);
    animation: sh-slide-in 0.5s var(--ease-out) forwards;
    animation-delay: calc(var(--i) * 70ms);
}
@keyframes sh-slide-in { to { opacity: 1; transform: none; } }

@media (prefers-reduced-motion: reduce) {
    #slide-nav { transition-duration: 0.001ms; }
    #slide-nav.isShown .reveal > * { animation: none; opacity: 1; transform: none; }
    #top-nav a::after { transition: none; }
}
</style>
