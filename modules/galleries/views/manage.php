<?php
/**
 * Galleries admin — manage panel.
 *
 * Consumed two ways:
 *  - via the admin_area sidebar (mx-select="#galleries-container" — the outer
 *    div gets stripped by Trongate MX, so the visual chrome lives on .gl-shell)
 *  - direct load through the default_admin theme (galleries/manage URL)
 * The inline <style> makes the panel self-contained in both contexts.
 */
?>
<div id="title" style="display:none"><h1>Galleries</h1></div>

<div id="stat-panel">
    <div class="gl-stats">
        <div class="gl-stat gl-stat--lead">
            <span class="gl-stat__num"><?= (int) $pagination_data['total_rows'] ?></span>
            <span class="gl-stat__label">Photo galleries</span>
        </div>
    </div>
</div>

<div id="galleries-container">

<style>
/* ============================================================
   GALLERIES ADMIN — charcoal base, single cyan accent
   (same design language as the other admin panels)
   ============================================================ */
@import url('https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600;700&display=swap');

:root {
  --gl-bg:        #15141a;
  --gl-surface:   #1c1b23;
  --gl-surface-2: #211f2a;
  --gl-line:      rgba(255,255,255,0.06);
  --gl-line-2:    rgba(255,255,255,0.11);
  --gl-text:      #f4f3f8;
  --gl-muted:     #a09db1;
  --gl-faint:     #6c6979;

  --gl-accent:    #45c4d6;
  --gl-green:     #2ec27e;
  --gl-red:       #e5484d;

  --gl-font: 'Geist', 'Segoe UI', system-ui, -apple-system, sans-serif;
  --gl-mono: 'Geist Mono', 'SF Mono', ui-monospace, Menlo, monospace;

  --gl-ease: cubic-bezier(0.16, 1, 0.3, 1);
}

#stat-cards { display: block !important; }

@keyframes gl-fade { from { opacity: 0; } }

/* ── Stat strip ──────────────────────────────────────────── */
#stat-panel { margin: 0.9rem 0.9rem 1.1rem; font-family: var(--gl-font); color: var(--gl-text); }
.gl-stats {
  display: grid;
  grid-template-columns: 1fr;
  border: 1px solid var(--gl-line-2);
  border-radius: 14px;
  overflow: hidden;
  font-family: var(--gl-font);
  color: var(--gl-text);
  animation: gl-fade 0.4s var(--gl-ease) both;
}
.gl-stat {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  padding: 0.95rem 1.15rem;
  background: var(--gl-surface);
}
.gl-stat--lead { background: linear-gradient(135deg, rgba(69,196,214,0.09), rgba(69,196,214,0.02) 65%), var(--gl-surface); }
.gl-stat__num {
  font-family: var(--gl-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
  letter-spacing: -0.03em;
  color: var(--gl-accent);
}
.gl-stat__label {
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--gl-muted);
}

/* ── Shell ───────────────────────────────────────────────── */
.gl-shell {
  margin: 0 0.9rem 1.5rem;
  padding: clamp(1.25rem, 2vw, 1.85rem);
  background: var(--gl-bg);
  border: 1px solid var(--gl-line);
  border-radius: 16px;
  color: var(--gl-text);
  font-family: var(--gl-font);
}
.gl-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.1rem;
}
.gl-eyebrow {
  margin: 0 0 0.45rem;
  font-family: var(--gl-mono);
  font-size: 0.62rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  color: var(--gl-accent);
}
.gl-title {
  margin: 0;
  font-size: clamp(1.4rem, 2.5vw, 1.8rem);
  font-weight: 700;
  line-height: 1;
  letter-spacing: -0.03em;
  color: var(--gl-text);
  text-transform: none;
}
.gl-tools { display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap; }

.gl-tools form {
  display: flex;
  flex-direction: row;
  gap: 0.4rem;
  width: auto;
  align-items: center;
  grid-template-columns: unset;
}
.gl-tools input[type="search"] {
  width: 190px;
  height: 36px;
  margin: 0 !important;
  padding: 0 0.7rem;
  border: 1px solid var(--gl-line-2);
  border-radius: 9px;
  background: var(--gl-surface);
  color: var(--gl-text);
  font-family: var(--gl-font);
  font-size: 0.82rem;
  text-align: left;
  box-sizing: border-box;
  box-shadow: none;
  outline: none;
  transition: border-color 0.15s var(--gl-ease), box-shadow 0.15s var(--gl-ease);
}
.gl-tools input[type="search"]:focus { border-color: var(--gl-accent); box-shadow: 0 0 0 3px rgba(69,196,214,0.18); }
.gl-tools input[type="submit"] {
  height: 36px;
  width: auto;
  margin: 0 !important;
  padding: 0 0.9rem;
  border: 1px solid var(--gl-line-2);
  border-radius: 9px;
  background: transparent;
  color: var(--gl-muted);
  font-family: var(--gl-font);
  font-size: 0.78rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s var(--gl-ease), color 0.15s var(--gl-ease);
}
.gl-tools input[type="submit"]:hover { background: rgba(255,255,255,0.05); color: var(--gl-text); }
.gl-per-page {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.66rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--gl-faint);
  white-space: nowrap;
}
.gl-per-page select {
  height: 36px;
  width: auto;
  padding: 0 0.5rem;
  border: 1px solid var(--gl-line-2);
  border-radius: 9px;
  background: var(--gl-surface);
  color: var(--gl-text);
  font-family: var(--gl-font);
  font-size: 0.78rem;
  outline: none;
}
.gl-btn-new {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  height: 36px;
  padding: 0 1rem;
  border: 1px solid var(--gl-accent) !important;
  border-radius: 9px;
  background: var(--gl-accent) !important;
  color: #08252a !important;
  font-family: var(--gl-font);
  font-size: 0.8rem;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  box-shadow: none;
  transition: filter 0.15s var(--gl-ease), transform 0.12s var(--gl-ease);
}
.gl-btn-new:hover { filter: brightness(1.08); color: #08252a !important; }
.gl-btn-new:active { transform: translateY(1px) scale(0.985); }

/* ── Flashdata + pagination ──────────────────────────────── */
.gl-shell .flashdata {
  display: block;
  margin: 0 0 0.9rem;
  padding: 0.55rem 0.8rem;
  background: rgba(46,194,126,0.12);
  border: 1px solid rgba(46,194,126,0.35);
  border-radius: 8px;
  color: var(--gl-green);
  font-size: 0.78rem;
}
.gl-shell .pagination {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  flex-wrap: wrap;
  margin: 0 0 1rem;
  font-family: var(--gl-mono);
  font-size: 0.74rem;
  color: var(--gl-faint);
}
.gl-shell .pagination a, .gl-shell .pagination span {
  display: inline-flex;
  align-items: center;
  padding: 0.3rem 0.6rem;
  border-radius: 7px;
  color: var(--gl-muted);
  text-decoration: none;
  background: transparent;
  border: 1px solid transparent;
}
.gl-shell .pagination a:hover { color: var(--gl-text); background: rgba(255,255,255,0.05); }
.gl-shell .pagination .active, .gl-shell .pagination a.active {
  background: var(--gl-accent);
  color: #08252a;
  font-weight: 700;
}

/* ── Year groups + session cards ─────────────────────────── */
.gl-year-groups { display: flex; flex-direction: column; gap: 1.75rem; }
.gl-year {
  display: flex;
  align-items: baseline;
  gap: 0.7rem;
  margin: 0 0 0.7rem;
  font-family: var(--gl-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 1.45rem;
  font-weight: 700;
  letter-spacing: -0.03em;
  color: var(--gl-text);
}
.gl-year small {
  font-size: 0.62rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--gl-faint);
}
.gl-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 0.6rem;
}
.gl-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 1.3rem 0.75rem 1.1rem;
  background: var(--gl-surface);
  border: 1px solid var(--gl-line-2);
  border-radius: 12px;
  text-decoration: none;
  color: var(--gl-text);
  animation: gl-fade 0.4s var(--gl-ease) backwards;
  animation-delay: min(calc(var(--i) * 30ms), 360ms);
  transition: border-color 0.15s var(--gl-ease), background 0.15s var(--gl-ease),
              transform 0.12s var(--gl-ease);
}
.gl-card:hover {
  border-color: rgba(69,196,214,0.5);
  background: var(--gl-surface-2);
  transform: translateY(-2px);
  color: var(--gl-text);
}
.gl-card:active { transform: translateY(0); }
.gl-card:focus-visible { outline: 2px solid var(--gl-accent); outline-offset: 2px; }
.gl-card svg { color: var(--gl-faint); transition: color 0.15s var(--gl-ease); }
.gl-card:hover svg { color: var(--gl-accent); }
.gl-card__label { font-size: 0.82rem; font-weight: 600; letter-spacing: -0.01em; }
.gl-card__cta {
  font-family: var(--gl-mono);
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--gl-faint);
  transition: color 0.15s var(--gl-ease);
}
.gl-card:hover .gl-card__cta { color: var(--gl-accent); }

/* ── Empty state ─────────────────────────────────────────── */
.gl-empty {
  padding: 3rem 1.5rem;
  text-align: center;
  border: 1px dashed var(--gl-line-2);
  border-radius: 12px;
  color: var(--gl-muted);
}
.gl-empty__title { margin: 0 0 0.4rem; font-size: 0.95rem; font-weight: 600; color: var(--gl-text); }
.gl-empty p { margin: 0; font-size: 0.8rem; }

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 33.75rem) {
  .gl-shell { padding: 1rem; border-radius: 12px; }
  .gl-tools { width: 100%; }
  .gl-tools form { flex: 1; }
  .gl-tools input[type="search"] { flex: 1; width: auto; }
  .gl-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
}
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
}
</style>

<div class="gl-shell">

    <div class="gl-head">
        <div>
            <p class="gl-eyebrow">Molas Surf Club &middot; Photo galleries</p>
            <h2 class="gl-title">Galleries</h2>
        </div>
        <div class="gl-tools" id="results-tbl">
            <?php
            echo form_open('galleries/manage/1/', array("method" => "get"));
            echo form_search('searchphrase', '', array("placeholder" => "Search galleries..."));
            echo form_submit('submit', 'Search');
            echo form_close();
            ?>
            <label class="gl-per-page">
                Per page
                <?php
                $dropdown_attr['onchange'] = 'setPerPage()';
                echo form_dropdown('per_page', $per_page_options, $selected_per_page, $dropdown_attr);
                ?>
            </label>
            <?= anchor('galleries/create', '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg> New gallery', array("class" => "gl-btn-new")) ?>
        </div>
    </div>

    <?php flashdata(); ?>
    <?= Pagination::display($pagination_data) ?>

    <?php if (count($rows) > 0): ?>
        <?php
        $by_year = [];
        foreach ($rows as $row) {
            $by_year[$row->year][] = $row;
        }
        krsort($by_year);
        ?>
        <div class="gl-year-groups">
            <?php foreach ($by_year as $year => $sessions): ?>
            <section>
                <h3 class="gl-year"><?= (int) $year ?> <small><?= count($sessions) ?> session<?= count($sessions) === 1 ? '' : 's' ?></small></h3>
                <div class="gl-grid">
                    <?php $i = 0; foreach ($sessions as $row): ?>
                    <a href="<?= BASE_URL ?>galleries/show/<?= $row->id ?>" class="gl-card" style="--i:<?= $i++ ?>">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="9" cy="9" r="2"/>
                            <path d="m21 15-3.1-3.1a2 2 0 0 0-2.8 0L6 21"/>
                        </svg>
                        <span class="gl-card__label">Session <?= out($row->pamaina) ?></span>
                        <span class="gl-card__cta">View &rarr;</span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endforeach; ?>
        </div>

        <?php if (count($rows) > 9):
            unset($pagination_data['include_showing_statement']);
            echo Pagination::display($pagination_data);
        endif; ?>

    <?php else: ?>
        <div class="gl-empty">
            <p class="gl-empty__title">No galleries found</p>
            <p>Create one with the &ldquo;New gallery&rdquo; button, or adjust the search.</p>
        </div>
    <?php endif; ?>

</div><!-- /.gl-shell -->
</div><!-- /#galleries-container -->
