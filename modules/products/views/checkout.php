<div class="container-xxl">
    <div class="summary">
        <div class="order-summary">
            <h3>Gavėjas:</h3>
            <p>VšĮ Banglentė</p>
            <p>Vėtros g. 8, Klaipėda, LT-94266, Lietuva</p>
            <h3>Užsakymas:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Prekė</th>
                        <th>Kiekis</th>
                        <th>Kaina</th>
                        <th>Suma</th>
                    </tr>
                </thead>
                <tbody>
                <?php $total = 0; foreach ($products as $product): ?>
                    <?php
                        $qty = $cart[$product->id];
                        $line_total = $qty * $product->price;
                        $total += $line_total;
                    ?>
                    <tr>
                        <td><?= $product->name ?></td>
                        <td><?= $qty ?></td>
                        <td>€<?= number_format($product->price, 2) ?></td>
                        <td>€<?= number_format($line_total, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right"><strong>Viso:</strong></td>
                        <td><strong>€<?= number_format($total, 2) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            <?= anchor('products/cart', 'Grįžti atgal', array('class' => 'button alt')) ?>
        </div>
        <div class="info-form">
            <?php 
                echo form_open($form_location, array('class' => 'highlight-errors'));
                echo form_label('Vardas Pavardė');
                echo form_input('customer_name', post('customer_name', true));
                echo validation_errors('customer_name');
                echo form_label('Telefonas');
                echo form_input('phone', post('phone', true));
                echo validation_errors('phone');
                echo form_label('Emailas');
                echo form_input('email', post('email', true));
                echo validation_errors('email');
                echo form_label('Pasirinkite atsiėmimo būdą');
                echo '<div class="delivery" style="align-items:center">';
                echo form_radio('delivery', 'atsiėmimas', true, ['id' => 'option1-input']);
                echo form_label('Parduotuvėje Vėtros g. 8', ['for' => 'option1-input']);
                echo form_radio('delivery', 'omniva', false, ['id' => 'option2-input']);
                echo form_label('<img src="https://www.omniva.lt/wp-content/themes/omniva/assets/dist/assets/img/logo/logo-sign.svg">Omniva paštomate', ['for' => 'option2-input', 'style' => 'gap:0.5rem; display:flex;']);
                echo '<div class="omniva">';
                echo form_label('Pasirinkite jums patogų paštomatą');
                $options = [];
                foreach ($locations as $location) {
                    if ($location['TYPE'] === '0' && $location['A0_NAME'] === 'LT') {
                        $options[$location['ZIP']] = $location['NAME'];
                    }
                }
                echo form_dropdown('address', $options, post('address', true), ['class' => 'dropdown']);
                echo validation_errors('address');
                echo '</div></div>';
                echo '<div class="d-flex" style="align-items:center">';
                echo form_checkbox('sutikimas', 1, post('sutikimas', true));
                echo form_label('Pažymėdamas langelį patvirtinu, kad ir sutinku su Sąlygomis bei Prekių ir pinigų grąžinimo tvarka.');
                echo '</div>';
                echo validation_errors('sutikimas');
                echo form_submit('submit', 'Apmokėti<i style="margin-left:0.5rem" class="fa fa-check" aria-hidden="true"></i>', array('class' => 'float-right success'));
                echo form_close();
            ?>
        </div>
    </div>
</div>

<style>
#option2-input, #option1-input {
    display: none;
}
.delivery {
    display: grid;
    grid-template-columns: repeat(2, auto);
    & label {
        padding: 0.5rem;
        border: 1px solid;
        margin: 0 auto;
        border-radius: 5px;
    }
    & img {
        width: 15px;
        height:15px;
        margin:auto;
    }
}

.delivery label:hover {
    background: #f0f0f0;
    cursor: pointer;
}
.delivery input:checked + label {
    background: #f0f0f0;
    border: 1px solid var(--primary);
    box-shadow: 0 0 5px var(--primary);
    cursor: pointer;
}
.delivery input:checked + label:hover {
    background: #f0f0f0;
    cursor: pointer;
}
#option2-input:checked ~ .omniva {
    display: block;
}
.omniva {
    display: none;
    grid-column: 1 / span 2;
    & label {
        margin: 1.4em 0 0.4em 0;
        clear: both;
        display: block;
        text-align: left;
        border: none;
        padding: 0;
    }
}

.summary {
    display:grid;
    background: floralwhite;
    gap:2rem;
    grid-template-columns:repeat(auto-fit, minmax(350px, 1fr));
    & h3 {
        font-size:1.5rem;
    }
}
.order-summary {
    width: 100%;
    border-collapse: collapse;
    padding-inline:2rem;
    margin-bottom: calc(1rem + 70px);
}
.info-form {
    padding:2rem;
    margin:auto;
    
}
.validation-error-report {
    margin:0!important;
}
.form-field-validation-error {
    background:none;
    border:none;
}
.order-summary th,
.order-summary td {
    border: none;
    padding: 10px;
    text-align: center;
}

.place-order-btn {
    padding: 12px 30px;
    font-size: 16px;
    background: #000;
    color: #fff;
    border: none;
    cursor: pointer;
}

.place-order-btn:hover {
    background: #333;
}
.validation-error-alert {
    display: none;
}
th {
    background-color: transparent;
    color: var(--primary-darker);
}
</style>