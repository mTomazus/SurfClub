<?php $user_info = Modules::run("competitions-users/_get_user_info") ?>
<section class="card pad span-12" aria-labelledby="competition-title">
    <!-- Header -->
    <div class="modal-title">
        <h2> <?= out($competition->name) . ' ' . out($competition->year ) ?> </h2>
        <h3> <?= out($organizer->organization) ?> </h3>
        <h3><?= out($competition->location ?? '—') ?></h3>
    </div> 

    <!-- Main -->
    <div class="modal-main">
        <!-- Quick facts -->
        <div class="modal-grid">
            <div style="display: flex;align-items: center;">
                <p class="label">Entry: </p>
                <p class="value chip">
                <?php
                    $entryType = $competition->entry_type ?? 'free';
                    if ($entryType === 'paid') {
                        $fee = isset($competition->entry_fee) ? number_format((float)$competition->entry_fee, 2) : '—';
                        $currency = $competition->currency ?? 'EUR';
                        echo $fee . ' ' . $currency;
                    } else {
                        echo 'Free';
                    }
                ?>
                </p>
            </div>
            <div style="display: flex;align-items: center;">
                <p class="label">Status: </p>
                <p class="value chip"><?= out($competition->status ?? 'open') ?></p>
            </div>
        </div>

            <?php
            // Helper to compute seats left if your divisions have capacity and registered_count
            // $hasSeatsInfo = !empty($divisions) && isset($divisions[0]['capacity']);
            ?>

        <!-- Join competition form -->
        <form mx-post="competitions-users/join/<?= $competition->id ?>" mx-close-on-success="true" mx-on-success="#registrations" id="joinForm" action="#" method="post">

            <fieldset class="divisions">
                <legend>Select your division</legend>

                <?php if (!empty($divisions)): ?>

                    <?php
                        // Build options array from divisions
                        $options = [];
                        foreach ($divisions as $division) {
                            // Use division id as key, name as value
                            $options[$division['name']] = $division['name'];
                        }
                    ?>
                    <?php echo form_dropdown('division', $options); ?>

                <?php else: ?>

                    <div class="empty">No divisions available yet.</div>
                <?php endif; ?>

            </fieldset>

            <label class="terms">
                <input type="checkbox" id="agreeRules" name="agree" value="1" required>
                I agree to the competition rules and terms.
            </label>

            <div class="actions">
                <button type="button" class="btn" onclick="closeModal()">Cancel</button>

                <?php
                $canJoin = ($competition->status ?? 'open') === 'open';
                $btnText = ($competition->entry_type ?? 'free') === 'paid' ? 'Proceed to Payment' : 'Join Now';
                ?>
                <button type="submit" class="btn" id="joinBtn" <?= $canJoin ? '' : 'disabled' ?>>
                <?= $btnText ?>
                </button>
            </div>

            <?php if (!$canJoin): ?>
                <p class="note">Registration is currently closed.</p>
            <?php endif; 
            echo form_close(); ?>
    </div>
</section>
        
<style>
  .hidden{display:none}
  .modal-title {margin-bottom:12px;}
    .modal-title h2 {text-align:center;margin-top: 0;font-size: 2rem;text-transform: uppercase;}
    .modal-title h3 {text-align:left;margin:0;font-size:1.2rem;}
  .modal-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin:8px 0 16px}

  .divisions{border:1px solid #eee;border-radius:12px;padding:12px;margin:10px 0 14px}
  .divisions legend{font-weight:700;padding:0 6px}
  .division-option{display:flex;align-items:center;justify-content:space-between;gap:10px;border:1px solid #eee;border-radius:10px;padding:10px 12px;margin:8px 0;cursor:pointer}
  .division-option input{margin-right:8px}
  .division-option.disabled{opacity:.5;cursor:not-allowed}
  .division-name{font-weight:600}
  .division-cap{font-size:.82rem;background:#f1f5f9;border-radius:999px;padding:2px 8px}
  .division-cap.full{background:#fee2e2}

  .terms{display:flex;gap:10px;align-items:center;font-size:.92rem;margin:8px 0 12px}
  .actions{display:flex;justify-content:flex-end;gap:10px}
  .note{font-size:.85rem;color:#a00;margin-top:8px}

  .btn:disabled{opacity:.6;cursor:not-allowed}
</style>
