<?php
/**
 * Products admin — single product page (default_admin theme).
 * Self-contained styling matching the other admin panels.
 *
 *  - Cover image: the single `products.image` column (used in shop listings),
 *    managed with the existing upload / delete-picture flow.
 *  - Gallery: additional pictures via trongate_filezone summary panel.
 *  - Comments + delete modals keep the admin.js wiring (token/baseUrl vars
 *    at the bottom; drawComments must stay defined for the filezone preview).
 */
$has_discount = isset($discount_price) && (float) $discount_price > 0;
$stock = (int) $in_stock;
$status_key = in_array(strtolower((string) $status), ['active', 'inactive', 'archived'], true) ? strtolower((string) $status) : 'inactive';
?>
<div class="pr">

<style>
@import url('https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600;700&display=swap');

:root {
  --pr-bg:        #15141a;
  --pr-surface:   #1c1b23;
  --pr-surface-2: #211f2a;
  --pr-line:      rgba(255,255,255,0.06);
  --pr-line-2:    rgba(255,255,255,0.11);
  --pr-text:      #f4f3f8;
  --pr-muted:     #a09db1;
  --pr-faint:     #6c6979;
  --pr-accent:    #45c4d6;
  --pr-green:     #2ec27e;
  --pr-amber:     #e0a64b;
  --pr-red:       #e5484d;
  --pr-font: 'Geist', 'Segoe UI', system-ui, -apple-system, sans-serif;
  --pr-mono: 'Geist Mono', 'SF Mono', ui-monospace, Menlo, monospace;
  --pr-ease: cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes pr-fade { from { opacity: 0; } }

.pr {
  margin: 0.9rem;
  padding: clamp(1.25rem, 2vw, 1.85rem);
  background: var(--pr-bg);
  border: 1px solid var(--pr-line);
  border-radius: 16px;
  color: var(--pr-text);
  font-family: var(--pr-font);
  animation: pr-fade 0.4s var(--pr-ease) both;
}
.pr .flashdata {
  display: block;
  margin: 0 0 0.9rem;
  padding: 0.55rem 0.8rem;
  background: rgba(46,194,126,0.12);
  border: 1px solid rgba(46,194,126,0.35);
  border-radius: 8px;
  color: var(--pr-green);
  font-size: 0.78rem;
}

/* ── Header ──────────────────────────────────────────────── */
.pr-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.1rem;
}
.pr-eyebrow {
  margin: 0 0 0.45rem;
  font-family: var(--pr-mono);
  font-size: 0.62rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  color: var(--pr-accent);
}
.pr-title {
  margin: 0;
  display: flex;
  align-items: baseline;
  gap: 0.6rem;
  flex-wrap: wrap;
  font-size: clamp(1.3rem, 2.4vw, 1.7rem);
  font-weight: 700;
  line-height: 1.1;
  letter-spacing: -0.03em;
  color: var(--pr-text);
}
.pr-id {
  font-family: var(--pr-mono);
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--pr-faint);
  border: 1px solid var(--pr-line-2);
  border-radius: 99px;
  padding: 0.15rem 0.5rem;
  align-self: center;
}
.pr-actions { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
.pr-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  height: 36px;
  padding: 0 0.95rem;
  margin: 0;
  border-radius: 9px;
  font-family: var(--pr-font);
  font-size: 0.78rem;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  box-shadow: none;
  transition: background 0.15s var(--pr-ease), color 0.15s var(--pr-ease),
              border-color 0.15s var(--pr-ease), transform 0.12s var(--pr-ease),
              filter 0.15s var(--pr-ease);
}
.pr-btn:active { transform: translateY(1px) scale(0.985); }
.pr-btn--ghost { background: transparent !important; border: 1px solid var(--pr-line-2) !important; color: var(--pr-muted) !important; }
.pr-btn--ghost:hover { background: rgba(255,255,255,0.05) !important; color: var(--pr-text) !important; }
.pr-btn--accent { background: var(--pr-accent) !important; border: 1px solid var(--pr-accent) !important; color: #08252a !important; font-weight: 700; }
.pr-btn--accent:hover { filter: brightness(1.08); color: #08252a !important; }
.pr-btn--danger { background: rgba(229,72,77,0.12) !important; border: 1px solid rgba(229,72,77,0.4) !important; color: var(--pr-red) !important; }
.pr-btn--danger:hover { background: rgba(229,72,77,0.22) !important; color: #fff !important; }

/* ── Panels ──────────────────────────────────────────────── */
.pr-media { display: grid; grid-template-columns: 320px 1fr; gap: 1rem; align-items: start; margin-bottom: 1rem; }
.pr-secondary { display: grid; grid-template-columns: 300px 1fr; gap: 1rem; align-items: start; }
.pr-panel {
  border: 1px solid var(--pr-line-2);
  border-radius: 12px;
  background: var(--pr-surface);
  overflow: hidden;
}
.pr-panel__head {
  font-size: 0.62rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--pr-faint);
  background: var(--pr-surface-2);
  padding: 0.6rem 0.9rem;
}
.pr-panel__body { padding: 0.9rem; }

/* Key / value details */
.pr-kv { display: flex; justify-content: space-between; gap: 1rem; padding: 0.5rem 0; border-bottom: 1px solid var(--pr-line); font-size: 0.82rem; }
.pr-kv:last-child { border-bottom: 0; }
.pr-kv > span:first-child { color: var(--pr-muted); }
.pr-kv > span:last-child { font-family: var(--pr-mono); font-feature-settings: 'tnum' 1; font-weight: 600; text-align: right; }
.pr-kv del { color: var(--pr-faint); font-weight: 500; margin-right: 0.4rem; }
.pr-kv ins { color: var(--pr-accent); text-decoration: none; }
.pr-desc { margin: 0.8rem 0 0; padding-top: 0.8rem; border-top: 1px solid var(--pr-line); font-size: 0.82rem; line-height: 1.6; color: var(--pr-muted); }
.pr-desc strong { display: block; margin-bottom: 0.35rem; font-size: 0.62rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--pr-faint); }

.pr-badge {
  display: inline-flex; align-items: center; gap: 0.35rem;
  padding: 0.15rem 0.55rem; border-radius: 99px;
  font-family: var(--pr-font) !important; font-size: 0.66rem; font-weight: 600; text-transform: capitalize;
}
.pr-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
.pr-badge--active   { color: var(--pr-green); background: rgba(46,194,126,0.12); }
.pr-badge--inactive { color: var(--pr-faint); background: rgba(255,255,255,0.06); }
.pr-badge--archived { color: var(--pr-amber); background: rgba(224,166,75,0.12); }

/* ── Cover image panel ───────────────────────────────────── */
.pr-cover__body { padding: 0.9rem; text-align: center; }
.pr-cover__img {
  width: 100%;
  border-radius: 10px;
  border: 1px solid var(--pr-line-2);
  display: block;
  background: var(--pr-bg);
}
.pr-cover__hint { margin: 0 0 0.7rem; font-size: 0.78rem; color: var(--pr-muted); }
.pr-cover__body form { width: 100%; display: block; margin: 0; }
.pr-cover__body input[type="file"] {
  width: 100%;
  margin: 0 0 0.7rem !important;
  padding: 0.5rem;
  font-family: var(--pr-font);
  font-size: 0.78rem;
  color: var(--pr-text);
  background: var(--pr-bg);
  border: 1px dashed var(--pr-line-2);
  border-radius: 9px;
  box-sizing: border-box;
}
.pr-cover__body input[type="submit"] {
  width: 100%;
  height: 38px;
  margin: 0 !important;
  border: 1px solid var(--pr-accent) !important;
  border-radius: 9px;
  background: var(--pr-accent) !important;
  color: #08252a !important;
  font-family: var(--pr-font);
  font-size: 0.8rem;
  font-weight: 700;
  cursor: pointer;
}
.pr-cover__body input[type="submit"]:hover { filter: brightness(1.08); }
.pr-cover__body .validation-error-report { text-align: left; }
.pr-cover__del { margin: 0.7rem 0 0; }

/* ── Filezone gallery reskin ─────────────────────────────── */
.pr-filezone .card {
  border: 1px solid var(--pr-line-2);
  border-radius: 12px;
  box-shadow: none;
  background: var(--pr-surface);
  overflow: hidden;
  margin: 0;
}
.pr-filezone .card-heading {
  font-family: var(--pr-font);
  font-size: 0.62rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--pr-faint);
  background: var(--pr-surface-2);
  border: none;
  padding: 0.6rem 0.9rem;
}
.pr-filezone .card-body { color: var(--pr-muted); padding: 0.75rem; }
.pr-filezone .card-body > p.text-center { padding: 0 0 0.6rem; margin: 0 0 0.6rem; }
.pr-filezone .button {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  height: 34px;
  padding: 0 0.9rem !important;
  border: 1px solid var(--pr-line-2) !important;
  border-radius: 9px;
  background: var(--pr-surface-2) !important;
  color: var(--pr-text) !important;
  font-family: var(--pr-font);
  font-size: 0.78rem;
  font-weight: 600;
  text-decoration: none;
  box-shadow: none;
}
.pr-filezone .button:hover { border-color: rgba(69,196,214,0.5) !important; color: var(--pr-accent) !important; }
.pr-filezone #gallery-pics {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 0.5rem;
  margin: 0;
}
.pr-filezone #gallery-pics > div {
  overflow: hidden;
  border-radius: 8px;
  border: 1px solid var(--pr-line-2);
  aspect-ratio: 1;
  background: var(--pr-bg);
  cursor: pointer;
  transition: border-color 0.15s var(--pr-ease), transform 0.12s var(--pr-ease);
}
.pr-filezone #gallery-pics > div:hover { border-color: rgba(69,196,214,0.5); transform: scale(1.02); }
.pr-filezone #gallery-pics > div img { width: 100%; height: 100%; object-fit: cover; display: block; padding: 0; }

/* ── Comments ────────────────────────────────────────────── */
.pr-panel #comments-block table { background: transparent; color: var(--pr-text); border: none; width: 100%; margin: 0; }
.pr-panel #comments-block td, .pr-panel #comments-block tr {
  background: transparent;
  border: none;
  border-bottom: 1px solid var(--pr-line);
  font-family: var(--pr-font);
  font-size: 0.8rem;
  text-align: left;
}

/* ── Modals (id-based: survive admin.js relocation to <body>) ─ */
#comment-modal.modal, #delete-modal.modal, #delete-picture-modal.modal, #preview-pic-modal.modal {
  background: var(--pr-surface);
  color: var(--pr-text);
  border: 1px solid var(--pr-line-2);
  border-radius: 16px;
  box-shadow: 0 24px 64px -24px rgba(0,0,0,0.6);
  font-family: var(--pr-font);
}
#comment-modal .modal-heading, #delete-modal .modal-heading, #delete-picture-modal .modal-heading, #preview-pic-modal .modal-heading {
  background: transparent;
  color: var(--pr-text);
  border: none;
  border-bottom: 1px solid var(--pr-line);
  border-radius: 16px 16px 0 0;
  font-weight: 600;
  margin-bottom: 0;
  -webkit-backdrop-filter: none;
  backdrop-filter: none;
}
#delete-modal .modal-heading.danger, #delete-picture-modal .modal-heading.danger { color: var(--pr-red); }
#comment-modal .modal-body, #delete-modal .modal-body, #delete-picture-modal .modal-body, #preview-pic-modal .modal-body {
  background: transparent;
  border: none;
  border-radius: 0 0 16px 16px;
  -webkit-backdrop-filter: none;
  backdrop-filter: none;
}
#comment-modal .modal-body p, #delete-modal .modal-body p, #delete-picture-modal .modal-body p { font-size: 0.85rem; color: var(--pr-muted); text-align: left; }
#preview-pic-modal .modal-body img { max-width: 100%; border-radius: 10px; }
#comment-modal textarea {
  width: 100%;
  min-height: 80px;
  padding: 0.6rem 0.7rem;
  border: 1px solid var(--pr-line-2);
  border-radius: 10px;
  background: var(--pr-bg);
  color: var(--pr-text);
  font-family: var(--pr-font);
  font-size: 0.85rem;
  resize: vertical;
  box-sizing: border-box;
  outline: none;
  transition: border-color 0.15s var(--pr-ease), box-shadow 0.15s var(--pr-ease);
}
#comment-modal textarea:focus { border-color: var(--pr-accent); box-shadow: 0 0 0 3px rgba(69,196,214,0.18); }
.pr-modal-btns { display: flex; gap: 0.5rem; justify-content: flex-end; flex-wrap: wrap; }
.pr-modal-btns button, .pr-modal-btns input[type="submit"] {
  width: auto;
  height: auto;
  padding: 0.52rem 1.05rem;
  margin: 0 !important;
  border: 1px solid transparent;
  border-radius: 9px;
  font-family: var(--pr-font);
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
}
.pr-btn-cancel { background: transparent !important; border-color: var(--pr-line-2) !important; color: var(--pr-muted) !important; }
.pr-btn-cancel:hover { background: rgba(255,255,255,0.05) !important; color: var(--pr-text) !important; }
.pr-btn-save { background: var(--pr-accent) !important; border-color: var(--pr-accent) !important; color: #08252a !important; font-weight: 700; }
.pr-btn-delete { background: var(--pr-red) !important; border-color: var(--pr-red) !important; color: #fff !important; font-weight: 700; }

@media (max-width: 56rem) {
  .pr-media { grid-template-columns: 1fr; }
  .pr-secondary { grid-template-columns: 1fr; }
}
@media (max-width: 43.75rem) {
  .pr { padding: 1rem; border-radius: 12px; }
  .pr-filezone #gallery-pics { grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); }
}
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
}
</style>

<?= flashdata() ?>

<div class="pr-head">
    <div>
        <p class="pr-eyebrow">Molas Surf Club &middot; Shop product</p>
        <h2 class="pr-title">
            <?= out($name) ?>
            <span class="pr-id">ID <?= out($update_id) ?></span>
        </h2>
    </div>
    <div class="pr-actions">
        <?= anchor('products/manage', '&larr; All products', array("class" => "pr-btn pr-btn--ghost")) ?>
        <?= anchor('products/create/' . $update_id, 'Edit details', array("class" => "pr-btn pr-btn--accent")) ?>
        <?php
        echo form_button('delete', 'Delete', array(
            "class" => "pr-btn pr-btn--danger",
            "onclick" => "openModal('delete-modal')"
        ));
        ?>
    </div>
</div>

<div class="pr-media">

    <!-- Cover image -->
    <div class="pr-panel">
        <div class="pr-panel__head">Cover image &middot; shown in shop</div>
        <div class="pr-cover__body">
            <?php
            if ($draw_picture_uploader == true) {
                echo form_open_upload(segment(1) . '/submit_upload_picture/' . $update_id);
                echo validation_errors();
                echo '<p class="pr-cover__hint">Choose a cover picture, then press Upload.</p>';
                echo form_file_select('picture');
                echo form_submit('submit', 'Upload');
                echo form_close();
            } else {
                $picture_path = BASE_URL . segment(1) . '_module/images/' . segment(1) . '_pics/' . $update_id . '/' . rawurlencode($image);
            ?>
                <img class="pr-cover__img" src="<?= $picture_path ?>" alt="Cover image">
                <p class="pr-cover__del">
                    <button type="button" class="pr-btn pr-btn--danger" onclick="openModal('delete-picture-modal')">Replace / delete cover</button>
                </p>
            <?php
            }
            ?>
        </div>
    </div>

    <!-- Gallery (additional pictures) -->
    <div class="pr-filezone">
        <?= Modules::run('trongate_filezone/_draw_summary_panel', $update_id, $filezone_settings) ?>
    </div>

</div>

<div class="pr-secondary">

    <!-- Details -->
    <div class="pr-panel">
        <div class="pr-panel__head">Product details</div>
        <div class="pr-panel__body">
            <div class="pr-kv"><span>Price</span><span>
                <?php if ($has_discount): ?>
                    <del>&euro;<?= number_format((float) $price, 2) ?></del><ins>&euro;<?= number_format((float) $discount_price, 2) ?></ins>
                <?php else: ?>
                    &euro;<?= number_format((float) $price, 2) ?>
                <?php endif; ?>
            </span></div>
            <div class="pr-kv"><span>In stock</span><span style="color:<?= $stock > 0 ? 'var(--pr-green)' : 'var(--pr-red)' ?>"><?= $stock ?></span></div>
            <div class="pr-kv"><span>Status</span><span><span class="pr-badge pr-badge--<?= $status_key ?>"><?= out($status) ?></span></span></div>
            <div class="pr-kv"><span>Record ID</span><span><?= out($update_id) ?></span></div>
            <?php if (!empty($short_desc)): ?>
                <div class="pr-desc"><strong>Short description</strong><?= nl2br(out($short_desc)) ?></div>
            <?php endif; ?>
            <?php if (!empty($description)): ?>
                <div class="pr-desc"><strong>Description</strong><?= nl2br(out($description)) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Comments -->
    <div class="pr-panel">
        <div class="pr-panel__head">Comments</div>
        <div class="pr-panel__body">
            <p style="margin:0 0 0.7rem"><button type="button" class="pr-btn pr-btn--ghost" onclick="openModal('comment-modal')">Add comment</button></p>
            <div id="comments-block"><table></table></div>
        </div>
    </div>

</div>

<!-- Comment modal -->
<div class="modal" id="comment-modal" style="display: none;">
    <div class="modal-heading">Add New Comment</div>
    <div class="modal-body">
        <p><textarea placeholder="Enter comment here..."></textarea></p>
        <div class="pr-modal-btns">
            <?php
            $attr_close = array("class" => "pr-btn-cancel", "onclick" => "closeModal()");
            echo form_button('close', 'Cancel', $attr_close);
            echo form_button('submit', 'Submit comment', array("class" => "pr-btn-save", "onclick" => "submitComment()"));
            ?>
        </div>
    </div>
</div>

<!-- Delete cover picture modal -->
<div class="modal" id="delete-picture-modal" style="display: none;">
    <div class="modal-heading danger">Delete Cover Picture</div>
    <div class="modal-body">
        <?= form_open(segment(1) . '/ditch_picture/' . $update_id) ?>
        <p>You are about to delete the cover picture. This cannot be undone. To set a new one, delete this and upload again.</p>
        <div class="pr-modal-btns">
            <?php
            echo form_button('close', 'Cancel', $attr_close);
            echo form_submit('submit', 'Yes - Delete Now', array("class" => 'pr-btn-delete'));
            ?>
        </div>
        <?= form_close() ?>
    </div>
</div>

<!-- Delete product modal -->
<div class="modal" id="delete-modal" style="display: none;">
    <div class="modal-heading danger">Delete Product</div>
    <div class="modal-body">
        <?= form_open('products/submit_delete/' . $update_id) ?>
        <p>You are about to delete this product record. This cannot be undone. Do you really want to do this?</p>
        <div class="pr-modal-btns">
            <?php
            echo form_button('close', 'Cancel', $attr_close);
            echo form_submit('submit', 'Yes - Delete Now', array("class" => 'pr-btn-delete'));
            ?>
        </div>
        <?= form_close() ?>
    </div>
</div>

</div><!-- /.pr -->

<script>
const token = '<?= $token ?>';
const baseUrl = '<?= BASE_URL ?>';
const segment1 = '<?= segment(1) ?>';
const updateId = '<?= $update_id ?>';
const drawComments = true;
</script>
