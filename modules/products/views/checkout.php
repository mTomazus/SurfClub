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
                        $effective_price = ($product->discount_price > 0) ? (float)$product->discount_price : (float)$product->price;
                        $line_total = $qty * $effective_price;
                        $total += $line_total;
                    ?>
                    <tr>
                        <td><?= out($product->name) ?></td>
                        <td><?= $qty ?></td>
                        <td>€<?= number_format($effective_price, 2) ?></td>
                        <td>€<?= number_format($line_total, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="total-label"><strong>Viso:</strong></td>
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
                echo '<div class="delivery">';
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
                echo '<div class="d-flex">';
                echo form_checkbox('sutikimas', 1, post('sutikimas', true));
                echo form_label('Pažymėdamas langelį patvirtinu, kad ir sutinku su Sąlygomis bei Prekių ir pinigų grąžinimo tvarka.');
                echo '</div>';
                echo validation_errors('sutikimas');
                echo '<button type="submit" class="float-right success">Apmokėti<i style="margin-left:0.5rem" class="fa fa-check" aria-hidden="true"></i></button>';
                echo form_close();
            ?>
        </div>
    </div>
</div>

<style>
/* ── Layout ─────────────────────────────────── */
.summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    background: white;
    border: 1px solid var(--border);
    margin-inline: clamp(0rem, 4vw, 2rem);
}

.order-summary {
    padding: 2.5rem 2rem 3rem;
    background: var(--secondary-color);
    border-right: 1px solid var(--border);
}

.info-form {
    padding: 2.5rem 2rem 3rem;
}

/* ── Order summary table ─────────────────────── */
.order-summary h3 {
    font-size: 0.68rem;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--primary);
    font-weight: 600;
    margin: 0 0 0.6rem;
    padding: 0;
}

.order-summary h3 + h3 { margin-top: 1.5rem; }

.order-summary table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.5rem;
}

.order-summary th {
    font-size: 0.68rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--clr-dark);
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    border-bottom: 1px solid var(--border);
    text-align: left;
    background: transparent;
}

.order-summary td {
    padding: 0.8rem 0.75rem;
    font-size: 0.875rem;
    color: var(--primary-darker);
    border-bottom: 1px solid hsl(0 0% 93%);
    text-align: left;
}

.order-summary th:not(:first-child),
.order-summary td:not(:first-child) { text-align: right; }

.order-summary tfoot td {
    border-bottom: none;
    font-weight: 600;
    font-size: 0.95rem;
    padding-top: 1rem;
}

.order-summary .total-label { text-align: right; }

.order-summary .button.alt {
    display: inline-block;
    font-size: 0.75rem;
    letter-spacing: 0.09em;
    text-transform: uppercase;
    color: var(--clr-dark);
    text-decoration: underline;
    text-underline-offset: 3px;
    margin-top: 1rem;
}

/* ── Form labels ─────────────────────────────── */
.info-form form label {
    display: block;
    font-size: 0.68rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    font-weight: 600;
    color: var(--primary-darker);
    margin: 1.25rem 0 0.4rem;
}

/* ── Text inputs ─────────────────────────────── */
.info-form form input[type="text"],
.info-form form input[type="email"],
.info-form form input[type="tel"] {
    display: block;
    width: 100%;
    height: 44px;
    padding: 0 0.875rem;
    border: 1px solid var(--border);
    background: white;
    font-family: inherit;
    font-size: 0.9rem;
    color: var(--primary-darker);
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    box-sizing: border-box;
    border-radius: 0;
    appearance: none;
    -webkit-appearance: none;
}

.info-form form input[type="text"]:hover,
.info-form form input[type="email"]:hover {
    border-color: var(--primary);
}

.info-form form input[type="text"]:focus,
.info-form form input[type="email"]:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--clr-primary-50);
}

/* ── Delivery card selector ──────────────────── */
#option1-input, #option2-input { display: none; }

.delivery {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.6rem;
    align-items: start !important;
    margin-top: 0.4rem;
}

.delivery > label {
    margin: 0 !important;
    padding: 0.875rem 0.75rem;
    border: 1px solid var(--border);
    background: white;
    font-size: 0.8rem;
    text-transform: none !important;
    letter-spacing: 0 !important;
    font-weight: 400 !important;
    text-align: center;
    cursor: pointer;
    display: flex !important;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    line-height: 1.3;
}

.delivery > label:hover {
    border-color: var(--primary);
    background: hsl(210 55% 97%);
}

.delivery input:checked + label {
    border-color: var(--primary);
    background: hsl(210 55% 95%);
    box-shadow: 0 0 0 2px var(--clr-primary-50);
    font-weight: 500 !important;
    cursor: default;
}

.delivery img {
    width: 16px;
    height: 16px;
    object-fit: contain;
}

/* ── Omniva select ───────────────────────────── */
.omniva {
    display: none;
    grid-column: 1 / -1;
}

#option2-input:checked ~ .omniva { display: block; }

.omniva > label {
    margin: 0.5rem 0 0.4rem !important;
}

select.dropdown,
.omniva select {
    display: block;
    width: 100%;
    height: 44px;
    padding: 0 2.5rem 0 0.875rem;
    border: 1px solid var(--border);
    background-color: white;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%234682b4' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.875rem center;
    background-size: 1rem;
    appearance: none;
    -webkit-appearance: none;
    font-family: inherit;
    font-size: 0.875rem;
    color: var(--primary-darker);
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    box-sizing: border-box;
    cursor: pointer;
}

select.dropdown:hover,
.omniva select:hover { border-color: var(--primary); }

select.dropdown:focus,
.omniva select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--clr-primary-50);
}

/* ── Checkbox row ────────────────────────────── */
.info-form .d-flex {
    display: flex !important;
    align-items: flex-start;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

.info-form .d-flex input[type="checkbox"] {
    width: 18px;
    height: 18px;
    min-width: 18px;
    margin-top: 2px;
    accent-color: var(--primary);
    cursor: pointer;
    flex-shrink: 0;
}

.info-form .d-flex label {
    font-size: 0.78rem !important;
    text-transform: none !important;
    letter-spacing: 0 !important;
    font-weight: 400 !important;
    color: var(--clr-dark);
    margin: 0 !important;
    line-height: 1.55;
}

/* ── Submit button ───────────────────────────── */
.info-form form button[type="submit"] {
    display: block;
    width: 100%;
    float: none;
    margin-top: 1.75rem;
    padding: 1rem;
    background: var(--primary-darker);
    color: white;
    border: none;
    font-family: inherit;
    font-size: 0.75rem;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    cursor: pointer;
    transition: background 0.2s;
}

.info-form form button[type="submit"]:hover { background: var(--primary); }
.info-form form button[type="submit"]:active { background: var(--primary-dark); }

/* ── Validation ──────────────────────────────── */
.validation-error-report {
    margin: 0.2rem 0 0 !important;
    font-size: 0.75rem;
    color: #b91c1c;
    letter-spacing: 0.02em;
}

.form-field-validation-error {
    background: none !important;
    border: none !important;
    border-left: 2px solid #b91c1c !important;
    padding-left: 0.5rem !important;
}

.validation-error-alert { display: none; }
</style>