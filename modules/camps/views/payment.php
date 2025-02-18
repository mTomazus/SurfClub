<?php

    echo anchor($everypay_url, 'sumoketi avansa', array("class" => "button xl"));

?>

<div class="two-col">
    <div class="card">
        <div class="card-heading">
            Registracijos Detalės
        </div>
        <div class="card-body">
            <div class="record-details">
                <div class="row">
                    <div>Vardas</div>
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
                <div class="row">
                    <div>Pamaina</div>
                    <div><?= out($pamaina) ?></div>
                </div>
                <div class="row">
                    <div>Amžius</div>
                    <div><?= out($age) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>