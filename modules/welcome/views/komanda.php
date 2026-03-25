<style>
  /* === SURF CLUB — KOMANDA SECTION === */
  @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,600;1,300&display=swap');

  :root {
    --ocean-deep: #0b2540;
    --ocean-mid:  #114a7a;
    --wave:       #1a9dc8;
    --foam:       #e8f6fb;
    --sand:       #f5e9d0;
    --sun:        #f4a623;
    --white:      #ffffff;
    --text-dark:  #0b2540;
    --text-muted: #5a7a95;
  }

  /* ---------- HERO BANNER ---------- */
  .sc-team-hero {
    position: relative;
    width: 100%;
    min-height: 520px;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
    background: var(--ocean-deep);
  }

  .sc-team-hero__photo {
    position: absolute;
    inset: 0;
    object-fit: cover;
    width: 100%;
    height: 100%;
    opacity: .72;
    filter: saturate(1.15);
  }

  /* fallback gradient when no real photo */
  .sc-team-hero__photo--placeholder {
    background: linear-gradient(135deg,
      #0b2540 0%, #114a7a 40%, #1a9dc8 75%, #5ed0f0 100%);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .sc-team-hero__photo--placeholder svg {
    width: 220px;
    opacity: .18;
  }

  .sc-team-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
      to top,
      rgba(11,37,64,.95) 0%,
      rgba(11,37,64,.35) 55%,
      transparent 100%
    );
  }

  /* animated wave border */
  .sc-team-hero__wave {
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    line-height: 0;
  }

  .sc-team-hero__content {
    position: relative;
    z-index: 2;
    padding: 3rem 6vw 5rem;
    max-width: 780px;
  }

  .sc-team-hero__eyebrow {
    font-family: 'Barlow', sans-serif;
    font-weight: 300;
    font-style: italic;
    font-size: clamp(.8rem, 1.4vw, 1rem);
    letter-spacing: .2em;
    color: var(--wave);
    text-transform: uppercase;
    margin-bottom: .6rem;
  }

  .sc-team-hero__title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(3.5rem, 10vw, 7.5rem);
    line-height: .92;
    color: var(--white);
    margin: 0 0 1.2rem;
    letter-spacing: .03em;
  }

  .sc-team-hero__title span {
    color: var(--sun);
  }

  .sc-team-hero__desc {
    font-family: 'Barlow', sans-serif;
    font-weight: 300;
    font-size: clamp(.95rem, 1.6vw, 1.15rem);
    line-height: 1.65;
    color: rgba(255,255,255,.78);
    max-width: 520px;
  }

  /* ---------- TEAM GRID ---------- */
  .sc-team-grid {
    background: var(--foam);
    padding: 5rem 5vw 6rem;
    position: relative;
  }

  .sc-team-grid::before {
    content: '';
    position: absolute;
    top: -1px; left: 0; right: 0;
    height: 6px;
    background: linear-gradient(90deg,
      var(--sun) 0%, var(--wave) 50%, var(--ocean-mid) 100%);
  }

  .sc-team-grid__heading {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(1.8rem, 4vw, 2.8rem);
    letter-spacing: .06em;
    color: var(--text-dark);
    margin: 0 0 .4rem;
  }

  .sc-team-grid__sub {
    font-family: 'Barlow', sans-serif;
    font-weight: 300;
    font-style: italic;
    color: var(--text-muted);
    font-size: 1rem;
    margin-bottom: 3rem;
    letter-spacing: .05em;
  }

  .sc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 2rem;
  }

  /* ---------- CARD ---------- */
  .sc-card {
    background: var(--white);
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 4px 28px rgba(11,37,64,.09);
    transition: transform .3s ease, box-shadow .3s ease;
    position: relative;
  }

  .sc-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 14px 40px rgba(11,37,64,.16);
  }

  .sc-card__img-wrap {
    position: relative;
    width: 100%;
    aspect-ratio: 3/4;
    overflow: hidden;
    background: linear-gradient(160deg,
      var(--ocean-mid) 0%, var(--wave) 100%);
  }

  .sc-card__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .5s ease;
  }

  .sc-card:hover .sc-card__img {
    transform: scale(1.05);
  }

  /* placeholder silhouette when no real image */
  .sc-card__img-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(160deg,
      var(--ocean-mid) 0%, var(--wave) 100%);
  }

  .sc-card__img-placeholder svg {
    width: 60%;
    opacity: .25;
  }

  /* accent stripe at top of card */
  .sc-card__stripe {
    height: 4px;
    width: 100%;
    background: var(--sun);
  }

  .sc-card:nth-child(2n) .sc-card__stripe { background: var(--wave); }
  .sc-card:nth-child(3n) .sc-card__stripe { background: var(--ocean-mid); }

  .sc-card__body {
    padding: 1.2rem 1.4rem 1.5rem;
  }

  .sc-card__name {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.45rem;
    letter-spacing: .05em;
    color: var(--text-dark);
    margin: 0 0 .15rem;
  }

  .sc-card__role {
    font-family: 'Barlow', sans-serif;
    font-weight: 600;
    font-size: .72rem;
    letter-spacing: .18em;
    text-transform: uppercase;
    color: var(--wave);
    margin-bottom: .8rem;
  }

  .sc-card__bio {
    font-family: 'Barlow', sans-serif;
    font-weight: 300;
    font-size: .9rem;
    line-height: 1.6;
    color: var(--text-muted);
  }

  .sc-card__social {
    display: flex;
    gap: .7rem;
    margin-top: 1rem;
  }

  .sc-card__social a {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--foam);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--ocean-mid);
    transition: background .2s, color .2s;
    text-decoration: none;
  }

  .sc-card__social a:hover {
    background: var(--ocean-deep);
    color: var(--white);
  }

  /* ---------- JOIN STRIP ---------- */
  .sc-team-join {
    background: var(--ocean-deep);
    padding: 4rem 6vw;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
  }

  .sc-team-join__text {
    color: var(--white);
  }

  .sc-team-join__label {
    font-family: 'Barlow', sans-serif;
    font-weight: 300;
    font-style: italic;
    font-size: .9rem;
    letter-spacing: .12em;
    color: var(--wave);
    text-transform: uppercase;
    margin-bottom: .5rem;
  }

  .sc-team-join__title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(1.8rem, 4vw, 3rem);
    letter-spacing: .04em;
    line-height: 1;
    margin: 0;
  }

  .sc-team-join__title em {
    color: var(--sun);
    font-style: normal;
  }

  .sc-team-join__cta {
    font-family: 'Barlow', sans-serif;
    font-weight: 600;
    font-size: .9rem;
    letter-spacing: .15em;
    text-transform: uppercase;
    color: var(--ocean-deep);
    background: var(--sun);
    padding: 1rem 2.5rem;
    border-radius: 3px;
    text-decoration: none;
    transition: background .25s, transform .2s;
    white-space: nowrap;
  }

  .sc-team-join__cta:hover {
    background: #f9bc50;
    transform: translateY(-2px);
  }

  /* ---------- RESPONSIVE ---------- */
  @media (max-width: 600px) {
    .sc-team-hero__content { padding-bottom: 4rem; }
    .sc-grid { grid-template-columns: 1fr 1fr; gap: 1rem; }
    .sc-team-join { text-align: center; justify-content: center; }
  }

  @media (max-width: 400px) {
    .sc-grid { grid-template-columns: 1fr; }
  }
</style>
<div class="wrapper container-xxl">
  <!-- ============================================================
       HERO — main team photo
       Replace src="#" with your actual group photo path
  ============================================================ -->
  <section class="sc-team-hero">

    <!-- ✏️ Replace this placeholder div with a real <img> tag:
         <img class="sc-team-hero__photo"
              src="/images/team-photo.jpg"
              alt="Surf Club komanda">
    -->
    <div class="sc-team-hero__photo sc-team-hero__photo--placeholder">
      <svg viewBox="0 0 200 120" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 80 C40 50,80 100,120 70 S180 40,200 60 V120 H0 Z" fill="white"/>
        <circle cx="60" cy="30" r="18" fill="white"/>
        <circle cx="100" cy="22" r="18" fill="white"/>
        <circle cx="140" cy="30" r="18" fill="white"/>
      </svg>
    </div>

    <div class="sc-team-hero__overlay"></div>

    <!-- animated SVG wave -->
    <div class="sc-team-hero__wave">
      <svg viewBox="0 0 1440 80" preserveAspectRatio="none"
           xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path fill="#e8f6fb"
          d="M0,40 C240,80 480,0 720,40 C960,80 1200,0 1440,40 L1440,80 L0,80 Z"/>
      </svg>
    </div>

    <div class="sc-team-hero__content">
      <p class="sc-team-hero__eyebrow">surfclub.lt — Klaipėda</p>
      <h1 class="sc-team-hero__title">Mūsų<br><span>Komanda</span></h1>
      <p class="sc-team-hero__desc">
        Mes esame banglenčių sporto entuziastai, treneriai ir vandens mylėtojai
        iš Baltijos jūros pakrantės. Kartu dalijamės aistra bangoms ir
        geriausiomis akimirkomis ant vandens.
      </p>
    </div>
  </section>


  <!-- ============================================================
       TEAM GRID
  ============================================================ -->
  <section class="sc-team-grid">
    <h2 class="sc-team-grid__heading">Susipažinkite su komanda</h2>
    <p class="sc-team-grid__sub">Kiekvienas žmogus — unikali istorija</p>

    <div class="sc-grid">

      <!-- CARD 1 -->
      <article class="sc-card">
        <div class="sc-card__stripe"></div>
        <div class="sc-card__img-wrap">
          <img class="sc-card__img" src="images/komanda/tomas.webp" alt="Tomas">
        </div>
        <div class="sc-card__body">
          <h3 class="sc-card__name">Tomas Ūksas</h3>
          <p class="sc-card__role">Vyriausiasis treneris</p>
          <p class="sc-card__bio">
            15 metų patirtis banglenčių sporte. ISA sertifikuotas teisėjas,
            čempionatų organizatorius ir Baltijos jūros bangų tyrinėtojas.
          </p>
          <div class="sc-card__social">
            <a href="#" aria-label="Instagram">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2">
                <rect x="2" y="2" width="20" height="20" rx="5"/>
                <circle cx="12" cy="12" r="4"/>
                <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
              </svg>
            </a>
            <a href="#" aria-label="Facebook">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2">
                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
              </svg>
            </a>
          </div>
        </div>
      </article>

      <!-- CARD 2 -->
      <article class="sc-card">
        <div class="sc-card__stripe"></div>
        <div class="sc-card__img-wrap">
            <img class="sc-card__img" src="images/komanda/evaldas.webp" alt="Evaldas">
        </div>
        <div class="sc-card__body">
          <h3 class="sc-card__name">Evaldas Daržinskas</h3>
          <p class="sc-card__role">Treneris & Instruktorius</p>
          <p class="sc-card__bio">
            Specializuojasi pradedančiųjų mokyme ir vandens saugos instruktavime.
            Aktyviai dalyvauja tarptautiniuose banglenčių sporto renginiuose.
          </p>
          <div class="sc-card__social">
            <a href="#" aria-label="Instagram">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2">
                <rect x="2" y="2" width="20" height="20" rx="5"/>
                <circle cx="12" cy="12" r="4"/>
                <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
              </svg>
            </a>
          </div>
        </div>
      </article>

      <!-- CARD 3 -->
      <article class="sc-card">
        <div class="sc-card__stripe"></div>
        <div class="sc-card__img-wrap">
            <img class="sc-card__img" src="images/komanda/laurynas.webp" alt="Laurynas">
        </div>
        <div class="sc-card__body">
          <h3 class="sc-card__name">Laurynas Kublickas</h3>
          <p class="sc-card__role">Treneris & Instruktorius</p>
          <p class="sc-card__bio">
            Vandens sporto entuziastas su 20 metų patirtimi. Rengia
            vasaros stovyklas jaunimui ir organizuoja jūros išvykas.
          </p>
          <div class="sc-card__social">
            <a href="#" aria-label="Instagram">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2">
                <rect x="2" y="2" width="20" height="20" rx="5"/>
                <circle cx="12" cy="12" r="4"/>
                <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
              </svg>
            </a>
            <a href="#" aria-label="Facebook">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2">
                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
              </svg>
            </a>
          </div>
        </div>
      </article>

      <!-- CARD 4 -->
      <article class="sc-card">
        <div class="sc-card__stripe"></div>
        <div class="sc-card__img-wrap">
            <img class="sc-card__img" src="images/komanda/laura.webp" alt="Laura">
        </div>
        <div class="sc-card__body">
          <h3 class="sc-card__name">Laura Kazlauskaitė</h3>
          <p class="sc-card__role">Teisėja & Koordinatorė</p>
          <p class="sc-card__bio">
            Rūpinasi klubo renginių organizavimu, bendruomene ir komunikacija.
            Stovyklų bei čempionatų koordinatorė su 12 metų patirtimi.
          </p>
          <div class="sc-card__social">
            <a href="#" aria-label="Instagram">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2">
                <rect x="2" y="2" width="20" height="20" rx="5"/>
                <circle cx="12" cy="12" r="4"/>
                <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
              </svg>
            </a>
          </div>
        </div>
      </article>

      <!-- CARD 5 -->
      <article class="sc-card">
        <div class="sc-card__stripe"></div>
        <div class="sc-card__img-wrap">
            <img class="sc-card__img" src="images/komanda/bernardas.webp" alt="Bernardas">
        </div>
        <div class="sc-card__body">
          <h3 class="sc-card__name">Bernardas Leščinskas</h3>
          <p class="sc-card__role">Sportininkas & Instruktorius</p>
          <p class="sc-card__bio">
            Daukartinis Lietuvos čempionas, aktyvus dalyvis tarptautinėse varžybose.
            Veda pažengusiųjų treniruotes ir dalijasi patirtimi su jaunimu.
          </p>
          <div class="sc-card__social">
            <a href="#" aria-label="Instagram">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2">
                <rect x="2" y="2" width="20" height="20" rx="5"/>
                <circle cx="12" cy="12" r="4"/>
                <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
              </svg>
            </a>
            <a href="#" aria-label="Facebook">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2">
                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
              </svg>
            </a>
          </div>
        </div>
      </article>

      <!-- CARD 6 -->
      <article class="sc-card">
        <div class="sc-card__stripe"></div>
        <div class="sc-card__img-wrap">
          <div class="sc-card__img-placeholder">
            <img class="sc-card__img" src="images/komanda/goda.webp" alt="Goda">
          </div>
        </div>
        <div class="sc-card__body">
          <h3 class="sc-card__name">Goda Staškevičiūtė</h3>
          <p class="sc-card__role">Instruktorė & Koordinatorė</p>
          <p class="sc-card__bio">
            Atsakinga už jaunimo programas ir vaikų stovyklas. Klaipėdos
            banglenčių sporto ambasadorė, dalijanti aistrą jūrai nuo 2017 m.
          </p>
          <div class="sc-card__social">
            <a href="#" aria-label="Instagram">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2">
                <rect x="2" y="2" width="20" height="20" rx="5"/>
                <circle cx="12" cy="12" r="4"/>
                <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
              </svg>
            </a>
          </div>
        </div>
      </article>

    </div><!-- /.sc-grid -->
  </section>


  <!-- ============================================================
       JOIN STRIP
  ============================================================ -->
  <section class="sc-team-join">
    <div class="sc-team-join__text">
      <p class="sc-team-join__label">Prisijunk prie mūsų</p>
      <h2 class="sc-team-join__title">Tapk <em>komandos</em> dalimi</h2>
    </div>
    <a href="mailto:info@surfclub.lt" class="sc-team-join__cta">
      Susisiek su mumis
    </a>
  </section>
</div>