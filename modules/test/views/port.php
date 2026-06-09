<style>
/* ── Design tokens ──────────────────────────────────────── */
.wx-page {
    --wx-glass:    rgba(8, 20, 42, 0.58);
    --wx-border:   rgba(255, 255, 255, 0.10);
    --wx-text:     rgba(255, 255, 255, 0.93);
    --wx-muted:    rgba(255, 255, 255, 0.48);
    --wx-radius:   18px;
    --wx-shadow:   0 8px 40px rgba(0, 0, 0, 0.4);
    --c-green:     #14532d;
    --c-lime:      #1a5c32;
    --c-amber:     #78350f;
    --c-red:       #7f1d1d;
    --font-mono:   'Courier New', ui-monospace, monospace;

    padding: 1rem 1rem 2rem;
    max-width: 52rem;
    margin-inline: auto;
    color: var(--wx-text);
    font-family: 'PT Serif Caption', serif;
}

/* ── Page header ────────────────────────────────────────── */
.wx-header {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    padding: 0.5rem 0 1.1rem;
    border-bottom: 1px solid var(--wx-border);
    margin-bottom: 1.1rem;
}
.wx-header__title {
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--wx-muted);
}
.wx-header__time {
    font-size: 0.65rem;
    letter-spacing: 0.08em;
    color: var(--wx-muted);
    font-family: var(--font-mono);
}

/* ── NOW card ───────────────────────────────────────────── */
.wx-now {
    background: var(--wx-glass);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    border: 1px solid var(--wx-border);
    border-radius: var(--wx-radius);
    box-shadow: var(--wx-shadow),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
    overflow: hidden;
    margin-bottom: 1.5rem;
    transition: background 0.5s ease;
}
.wx-now[data-rating="puikios"]   { border-left: 4px solid #22c55e; }
.wx-now[data-rating="geros"]     { border-left: 4px solid #86efac; }
.wx-now[data-rating="vidutinės"] { border-left: 4px solid #fb923c; }
.wx-now[data-rating="blogos"]    { border-left: 4px solid #f87171; }

.wx-now__top {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 0.75rem;
    padding: 1.25rem 1.4rem 0.75rem;
    align-items: start;
}
.wx-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.58rem;
    font-weight: 800;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 20px;
    padding: 3px 11px;
    color: var(--wx-muted);
    margin-bottom: 0.5rem;
}
.wx-badge__dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--wx-muted);
    flex-shrink: 0;
    animation: wx-pulse 2.4s ease-in-out infinite;
}
.wx-now[data-rating="puikios"]   .wx-badge__dot { background: #22c55e; }
.wx-now[data-rating="geros"]     .wx-badge__dot { background: #86efac; }
.wx-now[data-rating="vidutinės"] .wx-badge__dot { background: #fb923c; }
.wx-now[data-rating="blogos"]    .wx-badge__dot { background: #f87171; }

.wx-now__label {
    font-size: 1.25rem;
    font-weight: 800;
    line-height: 1.2;
    margin: 0 0 0.4rem;
    color: var(--wx-text);
    letter-spacing: -0.01em;
}
.wx-now__advice {
    font-size: 0.82rem;
    line-height: 1.5;
    color: var(--wx-muted);
    margin: 0;
    font-family: 'PT Serif', serif;
    font-style: italic;
}
.wx-now__wave-vis {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
    min-width: 56px;
}
.wx-wave-num {
    font-family: var(--font-mono);
    font-size: 2.2rem;
    font-weight: 700;
    line-height: 1;
    color: var(--wx-text);
    letter-spacing: -0.03em;
}
.wx-wave-unit {
    font-size: 0.6rem;
    letter-spacing: 0.1em;
    color: var(--wx-muted);
    text-transform: uppercase;
}

.wx-now__metrics {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    border-top: 1px solid var(--wx-border);
}
.wx-metric {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
    padding: 0.65rem 0.5rem;
}
.wx-metric + .wx-metric { border-left: 1px solid var(--wx-border); }
.wx-metric__val {
    font-family: var(--font-mono);
    font-size: 1rem;
    font-weight: 700;
    color: var(--wx-text);
    letter-spacing: -0.02em;
}
.wx-metric__key {
    font-size: 0.55rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    color: var(--wx-muted);
    text-transform: uppercase;
    text-align: center;
}

/* ── Section title ──────────────────────────────────────── */
.wx-section-title {
    font-size: 0.6rem;
    font-weight: 800;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--wx-muted);
    margin: 0 0 0.75rem;
}

/* ── Forecast rail ──────────────────────────────────────── */
.wx-rail {
    display: grid;
    grid-template-columns: 1.35fr 1fr 1fr 1fr;
    gap: 0.6rem;
}

.wx-day {
    background: var(--wx-glass);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid var(--wx-border);
    border-radius: 14px;
    padding: 0.85rem 0.75rem;
    box-shadow: var(--wx-shadow);
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    position: relative;
    overflow: hidden;
    transition: transform 0.22s cubic-bezier(.16,1,.3,1);
}
.wx-day:hover { transform: translateY(-2px); }

.wx-day[data-rating="puikios"]   { border-top: 3px solid #22c55e; }
.wx-day[data-rating="geros"]     { border-top: 3px solid #86efac; }
.wx-day[data-rating="vidutinės"] { border-top: 3px solid #fb923c; }
.wx-day[data-rating="blogos"]    { border-top: 3px solid #f87171; }

.wx-day__name {
    font-size: 0.58rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--wx-muted);
}
.wx-day__date {
    font-size: 0.6rem;
    color: var(--wx-muted);
    font-family: var(--font-mono);
    margin-bottom: 0.1rem;
}
.wx-day__waves {
    font-family: var(--font-mono);
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--wx-text);
    letter-spacing: -0.02em;
    line-height: 1;
}
.wx-day__wave-unit {
    font-size: 0.55rem;
    color: var(--wx-muted);
    letter-spacing: 0.06em;
    margin-bottom: 0.15rem;
}
.wx-day__wind {
    font-size: 0.7rem;
    color: var(--wx-muted);
    display: flex;
    align-items: center;
    gap: 4px;
    font-family: var(--font-mono);
}
.wx-day__wind-arrow {
    display: inline-block;
    width: 12px;
    height: 12px;
    opacity: 0.7;
}
.wx-day__badge {
    font-size: 0.55rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--wx-muted);
    margin-top: auto;
}
.wx-day[data-rating="puikios"]   .wx-day__badge { color: #86efac; }
.wx-day[data-rating="geros"]     .wx-day__badge { color: #a7f3c0; }
.wx-day[data-rating="vidutinės"] .wx-day__badge { color: #fdba74; }
.wx-day[data-rating="blogos"]    .wx-day__badge { color: #fca5a5; }

/* Sparkline */
.wx-spark {
    height: 28px;
    margin-top: 0.25rem;
    color: rgba(255, 255, 255, 0.3);
}
.wx-day[data-rating="puikios"]   .wx-spark { color: rgba(34, 197, 94, 0.5); }
.wx-day[data-rating="geros"]     .wx-spark { color: rgba(134, 239, 172, 0.5); }
.wx-day[data-rating="vidutinės"] .wx-spark { color: rgba(251, 146, 60, 0.5); }
.wx-day[data-rating="blogos"]    .wx-spark { color: rgba(248, 113, 113, 0.5); }
.wx-spark svg { width: 100%; height: 100%; display: block; }

/* ── Live sensors strip ─────────────────────────────────── */
.wx-live {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    border: 1px solid var(--wx-border);
    border-radius: 12px;
    margin-top: 1.25rem;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.04);
}
.wx-sensor {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    padding: 0.75rem 0.5rem;
}
.wx-sensor + .wx-sensor { border-left: 1px solid var(--wx-border); }
.wx-sensor__val {
    font-family: var(--font-mono);
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--wx-text);
}
.wx-sensor__key {
    font-size: 0.54rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    color: var(--wx-muted);
    text-transform: uppercase;
    text-align: center;
}

/* ── Skeleton shimmer ───────────────────────────────────── */
.wx-skel {
    background: linear-gradient(90deg,
        rgba(255,255,255,0.06) 0%,
        rgba(255,255,255,0.14) 50%,
        rgba(255,255,255,0.06) 100%);
    background-size: 200% 100%;
    animation: wx-shimmer 1.5s linear infinite;
    border-radius: 6px;
    color: transparent !important;
    user-select: none;
}

/* ── Animations ─────────────────────────────────────────── */
@keyframes wx-shimmer {
    from { background-position: 200% 0; }
    to   { background-position: -200% 0; }
}
@keyframes wx-pulse {
    0%, 100% { opacity: 0.5; transform: scale(1); }
    50%       { opacity: 1;   transform: scale(1.25); }
}
@keyframes wx-in {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: none; }
}
.wx-day { animation: wx-in 0.4s cubic-bezier(.16,1,.3,1) both; }
.wx-day:nth-child(1) { animation-delay: 0ms; }
.wx-day:nth-child(2) { animation-delay: 55ms; }
.wx-day:nth-child(3) { animation-delay: 110ms; }
.wx-day:nth-child(4) { animation-delay: 165ms; }

/* ── Responsive ─────────────────────────────────────────── */
@media (max-width: 48rem) {
    .wx-rail {
        grid-template-columns: repeat(4, 72vw);
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scroll-padding: 0 1rem;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .wx-rail::-webkit-scrollbar { display: none; }
    .wx-day { scroll-snap-align: start; }
    .wx-now__metrics { grid-template-columns: repeat(2, 1fr); }
    .wx-now__metrics .wx-metric:nth-child(3) { border-left: none; }
    .wx-live { grid-template-columns: repeat(2, 1fr); }
    .wx-sensor:nth-child(3) { border-left: none; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.001ms !important;
        transition-duration: 0.001ms !important;
    }
}
</style>

<div class="wx-page">

    <div class="wx-header">
        <span class="wx-header__title">Orai &middot; Melnrag&#279;</span>
        <span class="wx-header__time" id="wx-time">&#8212;</span>
    </div>

    <!-- Current AI conditions -->
    <div class="wx-now" id="wx-now" data-rating="">
        <div class="wx-now__top">
            <div>
                <div><span class="wx-badge"><span class="wx-badge__dot"></span><span id="now-badge">Kraunama</span></span></div>
                <h2 class="wx-now__label" id="now-label"><span class="wx-skel" style="display:inline-block;width:180px;height:1.25rem;">&nbsp;</span></h2>
                <p class="wx-now__advice" id="now-advice"><span class="wx-skel" style="display:inline-block;width:220px;height:0.85rem;">&nbsp;</span></p>
            </div>
            <div class="wx-now__wave-vis">
                <span class="wx-wave-num" id="now-wave-h"><span class="wx-skel" style="display:inline-block;width:48px;height:2.2rem;">&nbsp;</span></span>
                <span class="wx-wave-unit">bangos m</span>
            </div>
        </div>
        <div class="wx-now__metrics">
            <div class="wx-metric">
                <span class="wx-metric__val" id="now-wind">&#8212;</span>
                <span class="wx-metric__key">V&#279;jas m/s</span>
            </div>
            <div class="wx-metric">
                <span class="wx-metric__val" id="now-period">&#8212;</span>
                <span class="wx-metric__key">Periodas s</span>
            </div>
            <div class="wx-metric">
                <span class="wx-metric__val" id="now-water"><?= $vanduo ? out($vanduo) : '&mdash;' ?></span>
                <span class="wx-metric__key">Vanduo &deg;C</span>
            </div>
            <div class="wx-metric">
                <span class="wx-metric__val" id="now-air">&#8212;</span>
                <span class="wx-metric__key">Oras &deg;C</span>
            </div>
        </div>
    </div>

    <!-- 4-day forecast -->
    <p class="wx-section-title">Prognoz&#279; &middot; 4 dienos</p>
    <div class="wx-rail" id="wx-rail">
        <?php for ($i = 0; $i < 4; $i++): ?>
        <div class="wx-day">
            <div class="wx-day__name wx-skel" style="height:0.6rem;width:60%;">&nbsp;</div>
            <div class="wx-day__date wx-skel" style="height:0.6rem;width:50%;margin-bottom:0.5rem;">&nbsp;</div>
            <div class="wx-day__waves wx-skel" style="height:1.1rem;width:70%;">&nbsp;</div>
            <div class="wx-day__wave-unit wx-skel" style="height:0.55rem;width:30%;">&nbsp;</div>
            <div class="wx-day__wind wx-skel" style="height:0.7rem;width:55%;">&nbsp;</div>
            <div class="wx-spark wx-skel" style="height:28px;">&nbsp;</div>
        </div>
        <?php endfor; ?>
    </div>

    <!-- Live sensors (portofklaipeda.lt + meteo.lt) -->
    <div class="wx-live" id="wx-live">
        <div class="wx-sensor">
            <span class="wx-sensor__val" id="live-wind">&#8212;</span>
            <span class="wx-sensor__key">V&#279;jas m/s</span>
        </div>
        <div class="wx-sensor">
            <span class="wx-sensor__val" id="live-dir">&#8212;</span>
            <span class="wx-sensor__key">Kryptis &deg;</span>
        </div>
        <div class="wx-sensor">
            <span class="wx-sensor__val" id="live-air2">&#8212;</span>
            <span class="wx-sensor__key">Oras &deg;C</span>
        </div>
        <div class="wx-sensor">
            <span class="wx-sensor__val"><?= $vanduo ? out($vanduo) : '&mdash;' ?></span>
            <span class="wx-sensor__key">Vanduo &deg;C</span>
        </div>
    </div>

</div><!-- .wx-page -->

<script>
(function () {
    'use strict';

    // ── Sparkline generator ──────────────────────────────────
    function makeSpark(vals) {
        if (!vals || vals.length < 2) return '';
        const min = Math.min(...vals);
        const max = Math.max(...vals);
        const range = max - min || 0.01;
        const W = 60, H = 26;
        const pts = vals.map((v, i) =>
            `${(i / (vals.length - 1)) * W},${H - ((v - min) / range) * (H - 2) - 1}`
        ).join(' ');
        return `<svg viewBox="0 0 ${W} ${H}" preserveAspectRatio="none" aria-hidden="true">` +
               `<polyline points="${pts}" fill="none" stroke="currentColor" stroke-width="1.8" ` +
               `stroke-linecap="round" stroke-linejoin="round"/></svg>`;
    }

    // ── Rating label map ─────────────────────────────────────
    const LABELS = { puikios: 'Puikios', geros: 'Geros', vidutinės: 'Vidutinės', blogos: 'Blogos' };

    // ── Day labels (Lithuanian short date) ───────────────────
    function fmtDate(iso) {
        const d = new Date(iso + 'T12:00:00');
        return d.toLocaleDateString('lt-LT', { weekday: 'short', day: 'numeric', month: 'numeric' });
    }

    // ── Load current AI conditions ───────────────────────────
    fetch('<?= BASE_URL ?>test/surf_rating')
        .then(r => r.json())
        .then(d => {
            const card = document.getElementById('wx-now');
            const r    = d.rating || 'nežinoma';
            card.dataset.rating = r;
            document.getElementById('wx-time').textContent    = d.fetched_at ? 'Atnaujinta ' + d.fetched_at : '';
            document.getElementById('now-badge').textContent  = (LABELS[r] || r) + ' sąlygos';
            document.getElementById('now-label').textContent  = d.label  || '';
            document.getElementById('now-advice').textContent = d.advice || '';
            document.getElementById('now-wave-h').textContent = d.wave_height != null ? d.wave_height : '—';
            document.getElementById('now-wind').textContent   = d.wind_speed  != null ? d.wind_speed  : '—';
            document.getElementById('now-period').textContent = d.wave_period != null ? d.wave_period : '—';
        })
        .catch(() => {
            document.getElementById('now-label').textContent = 'Nepavyko gauti duomenų';
        });

    // ── Load 4-day forecast ──────────────────────────────────
    fetch('<?= BASE_URL ?>test/surf_forecast')
        .then(r => r.json())
        .then(d => {
            const rail = document.getElementById('wx-rail');
            rail.innerHTML = '';
            (d.days || []).forEach((day, i) => {
                const r      = day.rating || 'nežinoma';
                const rLabel = LABELS[r] || r;
                const el     = document.createElement('div');
                el.className     = 'wx-day';
                el.dataset.rating = r;
                el.style.setProperty('animation-delay', (i * 55) + 'ms');
                el.innerHTML = `
                    <div class="wx-day__name">${day.label}</div>
                    <div class="wx-day__date">${fmtDate(day.date)}</div>
                    <div class="wx-day__waves">${day.wave_min}&thinsp;–&thinsp;${day.wave_max}</div>
                    <div class="wx-day__wave-unit">bangos m</div>
                    <div class="wx-day__wind">
                        <svg class="wx-day__wind-arrow" viewBox="0 0 12 12" fill="none" aria-hidden="true"
                             style="transform:rotate(${windDeg(day.wind_dir)}deg)">
                            <path d="M6 1v10M6 1L3 4M6 1l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        ${day.wind_avg != null ? day.wind_avg : '—'}&thinsp;m/s&thinsp;${day.wind_dir || ''}
                    </div>
                    <div class="wx-spark">${makeSpark(day.spark)}</div>
                    <div class="wx-day__badge">${rLabel}</div>`;
                rail.appendChild(el);
            });
        })
        .catch(() => {
            document.getElementById('wx-rail').innerHTML =
                '<p style="color:rgba(255,255,255,0.4);font-size:0.8rem;grid-column:1/-1;padding:1rem 0">Prognoz&#279; laikinai nepasiekiama.</p>';
        });

    // Wind direction degrees for SVG arrow rotation (compass → degrees from North)
    function windDeg(compass) {
        const map = { 'Š':0, 'ŠR':45, 'R':90, 'PR':135, 'P':180, 'PV':225, 'V':270, 'ŠV':315 };
        return map[compass] ?? 0;
    }

    // ── Live portofklaipeda.lt sensors ──────────────────────
    [['wind_speed','live-wind'], ['wind_direction','live-dir'], ['air_temparature_first','live-air2'], ['air_temparature_first','now-air']]
        .forEach(([method, id]) => {
            fetch('https://portofklaipeda.lt/wp-json/api/meteo_data?method=' + method)
                .then(r => r.json())
                .then(rows => {
                    const val = parseFloat(rows[rows.length - 1][1]).toFixed(1);
                    const el  = document.getElementById(id);
                    if (el) el.textContent = val;
                })
                .catch(() => {});
        });
})();
</script>
