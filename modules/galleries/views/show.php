<?php
/**
 * Galleries admin — single gallery page (default_admin theme).
 * Self-contained styling; keeps the trongate_filezone summary panel and the
 * comments/delete modal wiring (token/baseUrl script vars at the bottom).
 */
?>
<div class="gls">

<style>
@import url('https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600;700&display=swap');

:root {
  --gls-bg:        #15141a;
  --gls-surface:   #1c1b23;
  --gls-surface-2: #211f2a;
  --gls-line:      rgba(255,255,255,0.06);
  --gls-line-2:    rgba(255,255,255,0.11);
  --gls-text:      #f4f3f8;
  --gls-muted:     #a09db1;
  --gls-faint:     #6c6979;
  --gls-accent:    #45c4d6;
  --gls-green:     #2ec27e;
  --gls-red:       #e5484d;
  --gls-font: 'Geist', 'Segoe UI', system-ui, -apple-system, sans-serif;
  --gls-mono: 'Geist Mono', 'SF Mono', ui-monospace, Menlo, monospace;
  --gls-ease: cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes gls-fade { from { opacity: 0; } }

.gls {
  margin: 0.9rem;
  padding: clamp(1.25rem, 2vw, 1.85rem);
  background: var(--gls-bg);
  border: 1px solid var(--gls-line);
  border-radius: 16px;
  color: var(--gls-text);
  font-family: var(--gls-font);
  animation: gls-fade 0.4s var(--gls-ease) both;
}
.gls .flashdata {
  display: block;
  margin: 0 0 0.9rem;
  padding: 0.55rem 0.8rem;
  background: rgba(46,194,126,0.12);
  border: 1px solid rgba(46,194,126,0.35);
  border-radius: 8px;
  color: var(--gls-green);
  font-size: 0.78rem;
}

/* ── Header ──────────────────────────────────────────────── */
.gls-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.1rem;
}
.gls-eyebrow {
  margin: 0 0 0.45rem;
  font-family: var(--gls-mono);
  font-size: 0.62rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  color: var(--gls-accent);
}
.gls-title {
  margin: 0;
  display: flex;
  align-items: baseline;
  gap: 0.6rem;
  font-size: clamp(1.4rem, 2.5vw, 1.8rem);
  font-weight: 700;
  line-height: 1;
  letter-spacing: -0.03em;
  color: var(--gls-text);
  text-transform: none;
}
.gls-title .gls-year { font-family: var(--gls-mono); }
.gls-title .gls-session { color: var(--gls-muted); font-weight: 600; font-size: 0.7em; }
.gls-id {
  font-family: var(--gls-mono);
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--gls-faint);
  border: 1px solid var(--gls-line-2);
  border-radius: 99px;
  padding: 0.15rem 0.5rem;
  align-self: center;
}
.gls-actions { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
.gls-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  height: 36px;
  padding: 0 0.95rem;
  margin: 0;
  border-radius: 9px;
  font-family: var(--gls-font);
  font-size: 0.78rem;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  box-shadow: none;
  transition: background 0.15s var(--gls-ease), color 0.15s var(--gls-ease),
              border-color 0.15s var(--gls-ease), transform 0.12s var(--gls-ease),
              filter 0.15s var(--gls-ease);
}
.gls-btn:active { transform: translateY(1px) scale(0.985); }
.gls-btn--ghost { background: transparent !important; border: 1px solid var(--gls-line-2) !important; color: var(--gls-muted) !important; }
.gls-btn--ghost:hover { background: rgba(255,255,255,0.05) !important; color: var(--gls-text) !important; }
.gls-btn--accent { background: var(--gls-accent) !important; border: 1px solid var(--gls-accent) !important; color: #08252a !important; font-weight: 700; }
.gls-btn--accent:hover { filter: brightness(1.08); color: #08252a !important; }
.gls-btn--danger { background: rgba(229,72,77,0.12) !important; border: 1px solid rgba(229,72,77,0.4) !important; color: var(--gls-red) !important; }
.gls-btn--danger:hover { background: rgba(229,72,77,0.22) !important; color: #fff !important; }

/* ── Filezone panel reskin ───────────────────────────────── */
.gls-filezone { margin-bottom: 1rem; }
.gls-filezone .card {
  border: 1px solid var(--gls-line-2);
  border-radius: 12px;
  box-shadow: none;
  background: var(--gls-surface);
  overflow: hidden;
}
.gls-filezone .card-heading {
  font-family: var(--gls-font);
  font-size: 0.62rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--gls-faint);
  background: var(--gls-surface-2);
  border: none;
  padding: 0.6rem 0.9rem;
}
.gls-filezone .card-body { color: var(--gls-muted); }
.gls-filezone #gallery-pics {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 0.5rem;
  padding: 0.75rem;
  margin: 0;
}
.gls-filezone #gallery-pics div {
  overflow: hidden;
  border-radius: 8px;
  border: 1px solid var(--gls-line-2);
  aspect-ratio: 1;
  background: var(--gls-bg);
  cursor: pointer;
  transition: border-color 0.15s var(--gls-ease), transform 0.12s var(--gls-ease);
}
.gls-filezone #gallery-pics div:hover { border-color: rgba(69,196,214,0.5); transform: scale(1.02); }
.gls-filezone #gallery-pics div img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  padding: 0;
  transition: transform 0.25s var(--gls-ease);
}
.gls-filezone #gallery-pics div:hover img { transform: scale(1.05); }
.gls-filezone .card-body > p.text-center { padding: 0.6rem 0.9rem; }
.gls-filezone .card-body a, .gls-filezone button {
  font-family: var(--gls-font);
}

/* ── Details + comments ──────────────────────────────────── */
.gls-secondary {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 1rem;
  align-items: start;
}
.gls-panel {
  border: 1px solid var(--gls-line-2);
  border-radius: 12px;
  background: var(--gls-surface);
  overflow: hidden;
}
.gls-panel__head {
  font-size: 0.62rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--gls-faint);
  background: var(--gls-surface-2);
  padding: 0.6rem 0.9rem;
}
.gls-panel__body { padding: 0.75rem 0.9rem; }
.gls-kv { display: flex; justify-content: space-between; gap: 1rem; padding: 0.45rem 0; border-bottom: 1px solid var(--gls-line); font-size: 0.8rem; }
.gls-kv:last-child { border-bottom: 0; }
.gls-kv span:first-child { color: var(--gls-muted); }
.gls-kv span:last-child { font-family: var(--gls-mono); font-feature-settings: 'tnum' 1; font-weight: 600; }
.gls-panel #comments-block table { background: transparent; color: var(--gls-text); border: none; width: 100%; }
.gls-panel #comments-block td, .gls-panel #comments-block tr {
  background: transparent;
  border: none;
  border-bottom: 1px solid var(--gls-line);
  font-family: var(--gls-font);
  font-size: 0.8rem;
  text-align: left;
}

/* ── Modals ──────────────────────────────────────────────── */
#comment-modal.modal, #delete-modal.modal {
  background: var(--gls-surface);
  color: var(--gls-text);
  border: 1px solid var(--gls-line-2);
  border-radius: 16px;
  box-shadow: 0 24px 64px -24px rgba(0,0,0,0.6);
  font-family: var(--gls-font);
}
#comment-modal .modal-heading, #delete-modal .modal-heading {
  background: transparent;
  color: var(--gls-text);
  border: none;
  border-bottom: 1px solid var(--gls-line);
  border-radius: 16px 16px 0 0;
  font-weight: 600;
  margin-bottom: 0;
  -webkit-backdrop-filter: none;
  backdrop-filter: none;
}
#delete-modal .modal-heading.danger { color: var(--gls-red); }
#comment-modal .modal-body, #delete-modal .modal-body {
  background: transparent;
  border: none;
  border-radius: 0 0 16px 16px;
  -webkit-backdrop-filter: none;
  backdrop-filter: none;
}
#comment-modal .modal-body p, #delete-modal .modal-body p { font-size: 0.85rem; color: var(--gls-muted); text-align: left; }
#comment-modal textarea {
  width: 100%;
  min-height: 80px;
  padding: 0.6rem 0.7rem;
  border: 1px solid var(--gls-line-2);
  border-radius: 10px;
  background: var(--gls-bg);
  color: var(--gls-text);
  font-family: var(--gls-font);
  font-size: 0.85rem;
  resize: vertical;
  box-sizing: border-box;
  outline: none;
  transition: border-color 0.15s var(--gls-ease), box-shadow 0.15s var(--gls-ease);
}
#comment-modal textarea:focus { border-color: var(--gls-accent); box-shadow: 0 0 0 3px rgba(69,196,214,0.18); }
.gls-modal-btns { display: flex; gap: 0.5rem; justify-content: flex-end; flex-wrap: wrap; }
.gls-modal-btns button, .gls-modal-btns input[type="submit"] {
  width: auto;
  height: auto;
  padding: 0.52rem 1.05rem;
  margin: 0 !important;
  border: 1px solid transparent;
  border-radius: 9px;
  font-family: var(--gls-font);
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
}
.gls-btn-cancel { background: transparent !important; border-color: var(--gls-line-2) !important; color: var(--gls-muted) !important; }
.gls-btn-cancel:hover { background: rgba(255,255,255,0.05) !important; color: var(--gls-text) !important; }
.gls-btn-save { background: var(--gls-accent) !important; border-color: var(--gls-accent) !important; color: #08252a !important; font-weight: 700; }
.gls-btn-delete { background: var(--gls-red) !important; border-color: var(--gls-red) !important; color: #fff !important; font-weight: 700; }

@media (max-width: 43.75rem) {
  .gls { padding: 1rem; border-radius: 12px; }
  .gls-secondary { grid-template-columns: 1fr; }
  .gls-filezone #gallery-pics { grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); }
}
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
}
</style>

<?= flashdata() ?>

<div class="gls-head">
    <div>
        <p class="gls-eyebrow">Molas Surf Club &middot; Photo gallery</p>
        <h2 class="gls-title">
            <span class="gls-year"><?= out($year) ?></span>
            <span class="gls-session">Session <?= out($pamaina) ?></span>
            <span class="gls-id">ID <?= out($update_id) ?></span>
        </h2>
    </div>
    <div class="gls-actions">
        <?= anchor('galleries/manage', '&larr; All galleries', array("class" => "gls-btn gls-btn--ghost")) ?>
        <?= anchor('galleries/create/' . $update_id, 'Edit', array("class" => "gls-btn gls-btn--accent")) ?>
        <?php
        $attr_delete = array(
            "class" => "gls-btn gls-btn--danger",
            "onclick" => "openModal('delete-modal')"
        );
        echo form_button('delete', 'Delete', $attr_delete);
        ?>
    </div>
</div>

<div class="gls-filezone">
    <?= Modules::run('trongate_filezone/_draw_summary_panel', $update_id, $filezone_settings) ?>
</div>

<div class="gls-secondary">
    <div class="gls-panel">
        <div class="gls-panel__head">Gallery details</div>
        <div class="gls-panel__body">
            <div class="gls-kv"><span>Year</span><span><?= out($year) ?></span></div>
            <div class="gls-kv"><span>Session</span><span><?= out($pamaina) ?></span></div>
            <div class="gls-kv"><span>Record ID</span><span><?= out($update_id) ?></span></div>
        </div>
    </div>

    <div class="gls-panel">
        <div class="gls-panel__head">Comments</div>
        <div class="gls-panel__body">
            <p style="margin:0 0 0.7rem"><button type="button" class="gls-btn gls-btn--ghost" onclick="openModal('comment-modal')">Add comment</button></p>
            <div id="comments-block"><table></table></div>
        </div>
    </div>
</div>

<!-- Comment modal -->
<div class="modal" id="comment-modal" style="display: none;">
    <div class="modal-heading">Add New Comment</div>
    <div class="modal-body">
        <p><textarea placeholder="Enter comment here..."></textarea></p>
        <div class="gls-modal-btns">
            <?php
            $attr_close = array("class" => "gls-btn-cancel", "onclick" => "closeModal()");
            echo form_button('close', 'Cancel', $attr_close);
            echo form_button('submit', 'Submit comment', array("class" => "gls-btn-save", "onclick" => "submitComment()"));
            ?>
        </div>
    </div>
</div>

<!-- Delete modal -->
<div class="modal" id="delete-modal" style="display: none;">
    <div class="modal-heading danger">Delete Gallery</div>
    <div class="modal-body">
        <?= form_open('galleries/submit_delete/' . $update_id) ?>
        <p>Are you sure you want to delete this gallery record? This cannot be undone.</p>
        <div class="gls-modal-btns">
            <?php
            echo form_button('close', 'Cancel', $attr_close);
            echo form_submit('submit', 'Yes - Delete Now', array("class" => 'gls-btn-delete'));
            ?>
        </div>
        <?= form_close() ?>
    </div>
</div>

</div><!-- /.gls -->

<script>
const token = '<?= $token ?>';
const baseUrl = '<?= BASE_URL ?>';
const segment1 = '<?= segment(1) ?>';
const updateId = '<?= $update_id ?>';
const drawComments = true;
</script>
