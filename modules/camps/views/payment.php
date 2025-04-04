<div id="response" style="background: #4BB543;grid-column: 1 / span 2;padding: 0.5rem 0;">
    <h2>FORMA SEKMINGAI IŠSIŲSTA</h2>
</div>
<div class="card-body" style="background:white; grid-column: 1 / span 2">
    <div class="record-details">
        <div class="row">
            <div>Vardas Pavardė</div>
            <div><?= out($name) ?></div>
        </div>
        <div class="row">
            <div>Telefonas</div>
            <div><?= out($phone) ?></div>
        </div>
        <div class="row">
            <div>Emailas</div>
            <div><?= out($email) ?></div>
        </div>
        <div class="row" style="grid-template-columns: 1fr 2fr;">
            <div>Pamaina</div>
            <div><?= out($pamaina) ?></div>
        </div>
        <div class="row">
            <div>Amžius</div>
            <div><?= out($age) ?> metai</div>
        </div>
    </div>
    <div>
        <p style="color: #555;font-size: 14px;font-family: PT Serif;">*Kad rezervuoti <strong>vietą stovykloje</strong> reikia sumokėti 100€ avansą.</p>
    </div>
    <div style="display:flex;justify-content:center">
        <?php echo anchor($everypay_url, 'Sumokėti avansą', array("class" => "button flex")); ?>
    </div>
</div>