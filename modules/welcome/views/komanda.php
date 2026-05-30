<style>
    :root {
        --txt-dark: #454545;
        --txt-white: white;
        --bg-white: white;
        --banglente: #2f78a8;
    }
    body {
        font-family: "Source Sans Pro", sans-serif;
        text-align: center;
        font-weight: 900;
        text-shadow: 0 0.2rem 0.5rem #00000075;
        color: var(--txt-white);
    }
    .title {
        margin: 1rem 0 0.5rem;
    }
    .komanda-intro {
        font-weight: 300;
        font-size: max(1rem, 1.3vw);
        letter-spacing: 0.02em;
        line-height: 1.7;
        color: rgba(255,255,255,0.75);
        text-shadow: none;
        max-width: 580px;
        margin: 0 auto 2rem;
        text-align: center;
    }
    h1, h2, h3 {
        text-align: center;
        text-transform: uppercase;
    }
    .komanda-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        width: 90%;
        margin: auto;
        gap: 2rem;
    }
    .k-card {
        display: flex;
        flex-direction: column;
        background: var(--bg-white);
        color: var(--txt-dark);
    }
    .k-card img {
        width: 100%;
        aspect-ratio: 3 / 4;
        object-fit: cover;
        object-position: top;
        display: block;
    }
    .k-card__no-photo {
        width: 100%;
        aspect-ratio: 3 / 4;
        background: #d0dde8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: #8aa8c0;
        font-weight: 900;
        text-shadow: none;
    }
    .k-card > div {
        padding: 1rem;
        text-align: center;
    }
    .k-card h3 {
        font-size: max(1.6rem, 1.8vw);
        margin-bottom: 0.4rem;
        color: var(--txt-dark);
        text-shadow: none;
    }
    .k-card h4 {
        font-size: max(1rem, 1.2vw);
        margin: 0 0 0.6rem;
        color: var(--banglente);
        text-shadow: none;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .k-card p {
        font-size: max(0.9rem, 1.1vw);
        font-weight: 400;
        color: var(--txt-dark);
        text-shadow: none;
        line-height: 1.5;
        margin: 0;
    }
    .k-join {
        width: 90%;
        margin: 2rem auto;
        text-align: center;
    }
    .k-join a {
        display: inline-block;
        background: var(--banglente);
        color: white;
        padding: 0.8rem 2.5rem;
        font-size: 1rem;
        font-weight: 900;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        text-decoration: none;
        text-shadow: none;
    }
    /* — card hover — */
    .k-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease; overflow: hidden;
    }
    .k-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.25);
    }
    .k-card img {
        transition: transform 0.4s ease;
    }
    .k-card:hover img {
        transform: scale(1.04);
    }
    .k-join a {
        transition: background 0.2s ease, transform 0.2s ease;
    }
    .k-join a:hover {
        background: #1e5f8a;
        transform: translateY(-2px);
    }

    /* — scroll reveal — */
    .fade-up {
        opacity: 0;
        transform: translateY(24px);
        transition: opacity 0.5s ease, transform 0.5s ease;
    }
    .fade-up.visible {
        opacity: 1;
        transform: translateY(0);
    }

    @media (prefers-reduced-motion: reduce) {
        .fade-up { transition: none; }
        .k-card  { transition: none; }
    }

    @media (max-width: 576px) {
        .komanda-grid { grid-template-columns: 1fr; gap: 1rem; }
    }
    @media (min-width: 577px) and (max-width: 1199px) {
        .komanda-grid { grid-template-columns: 1fr 1fr; gap: 2rem; }
    }
    @media (min-width: 1200px) {
        .komanda-grid { grid-template-columns: 1fr 1fr 1fr; gap: 2rem; }
    }
</style>

<h1 class="title fade-up">KOMANDA</h1>
<p class="komanda-intro fade-up">Mes — maža, bet stipri komanda, kurią jungia vienas dalykas: meilė jūrai. Treneriai, teisėjai, instruktoriai ir entuziastai, kiekvieną sezoną nešantys Baltijos banglenčių kultūrą į priekį.</p>

<div class="komanda-grid">

    <div class="k-card fade-up">
        <img src="images/komanda/tomas.webp" alt="Tomas Ūksas">
        <div>
            <h3>Tomas Ūksas</h3>
            <h4>Vyriausiasis treneris</h4>
            <p>15 metų patirtis banglenčių sporte. ISA sertifikuotas teisėjas, čempionatų organizatorius ir Baltijos bangų tyrinėtojas.</p>
        </div>
    </div>

    <div class="k-card fade-up">
        <img src="images/komanda/evaldas.webp" alt="Evaldas Daržinskas">
        <div>
            <h3>Evaldas Daržinskas</h3>
            <h4>Treneris &amp; Instruktorius</h4>
            <p>Specializuojasi pradedančiųjų mokyme ir vandens saugos instruktavime. Aktyviai dalyvauja tarptautiniuose renginiuose.</p>
        </div>
    </div>

    <div class="k-card fade-up">
        <img src="images/komanda/aleksas.webp" alt="Aleksas Eidukevičius">
        <div>
            <h3>Aleksas Eidukevičius</h3>
            <h4>Instruktorius &amp; Ekspertas</h4>
            <p>Pradedančiųjų mokymas ir stovyklos veiklos tobulinimas. Aktyviai prisideda prie klubo renginių organizavimo.</p>
        </div>
    </div>

    <div class="k-card fade-up">
        <img src="images/komanda/laurynas.webp" alt="Laurynas Kublickas">
        <div>
            <h3>Laurynas Kublickas</h3>
            <h4>Treneris &amp; Instruktorius</h4>
            <p>Vandens sporto entuziastas su 20 metų patirtimi. Rengia vasaros stovyklas jaunimui ir organizuoja jūros išvykas.</p>
        </div>
    </div>

    <div class="k-card fade-up">
        <img src="images/komanda/laura.webp" alt="Laura kazlauskaite">
        <div>
            <h3>Laura Kazlauskaitė</h3>
            <h4>Teisėja &amp; Koordinatorė</h4>
            <p>Rūpinasi renginių organizavimu ir bendruomene. Stovyklų bei čempionatų koordinatorė su 12 metų patirtimi.</p>
        </div>
    </div>

    <div class="k-card fade-up">
        <img src="images/komanda/bernardas.webp" alt="Bernardas Leščinskas">
        <div>
            <h3>Bernardas Leščinskas</h3>
            <h4>Sportininkas &amp; Instruktorius</h4>
            <p>Daukartinis Lietuvos čempionas, aktyvus tarptautinių varžybų dalyvis. Veda pažengusiųjų treniruotes.</p>
        </div>
    </div>

    <div class="k-card fade-up">
        <img src="images/komanda/goda.webp" alt="Goda Staškevičiūtė">
        <div>
            <h3>Goda Staškevičiūtė</h3>
            <h4>Instruktorė &amp; Koordinatorė</h4>
            <p>Atsakinga už jaunimo programas ir vaikų stovyklas. Klaipėdos banglenčių sporto ambasadorė nuo 2017 m.</p>
        </div>
    </div>

    <div class="k-card fade-up">
        <img src="images/komanda/evelina.webp" alt="Evelina Ketvirtytė">
        <div>
            <h3>Evelina Ketvirtytė</h3>
            <h4>Veiklų generatorė - Koordinatorė</h4>
            <p>Atsakinga už jaunimo programų generavimą. Klaipėdos banglenčių sporto varžybų dalyvė nuo 2020 m.</p>
        </div>
    </div>

    <div class="k-card fade-up">
        <img src="images/komanda/agne.webp" alt="Аgnė Jogutytė">
        <div>
            <h3>Аgnė Jogutytė</h3>
            <h4>Medių planavimas / vadyba</h4>
            <p>Atsakinga už medias ir marketingą. Soc. tinklų idėjų generavimas, valdymas ir priežiūra</p>
        </div>
    </div>
    
</div>

<div class="k-join fade-up">
    <p>Nori prisijungti prie komandos?</p>
    <a href="mailto:info@surfclub.lt">Susisiek su mumis</a>
</div>

<script>
(function () {
    var els = document.querySelectorAll('.fade-up');
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e, i) {
            if (!e.isIntersecting) return;
            var el = e.target;
            var siblings = Array.from(el.parentElement.querySelectorAll('.fade-up'));
            var idx = siblings.indexOf(el);
            el.style.transitionDelay = (idx * 80) + 'ms';
            el.classList.add('visible');
            io.unobserve(el);
        });
    }, { threshold: 0.1 });
    els.forEach(function (el) { io.observe(el); });
})();
</script>
