<div style="display:grid;grid-template-columns:repeat( auto-fit, minmax(450px, 1fr) )" class="container">
    <div class="" style="padding:1rem;display:flex;flex-direction:column">
        <img src="images/logo-150.png" alt="surfclub logo" style="margin:1rem auto">
        <h2 style="color:black;text-align:center">Dėkojame , jūsų registracija patvirtinta!</h2>
        <img src="images/surfer.png" alt="surfclub logo" style="margin:1rem auto">
    </div>
    <div class="" style="color:white;padding:1rem;display:flex;flex-direction:column;justify-content:space-evenly">
    <div class="record-details">
        <h2>Registracijos detalės:</h2>
        <div class="row">
            <div>Vardas Pavardė</div>
            <div><?= out($camp->name) ?></div>
        </div>
        <div class="row">
            <div>Telefonas</div>
            <div><?= out($camp->phone) ?></div>
        </div>
        <div class="row">
            <div>Emailas</div>
            <div><?= out($camp->email) ?></div>
        </div>
        <div class="row" style="grid-template-columns: 1fr 2fr;">
            <div>Pamaina</div>
            <div><?= out($camp->pamaina) ?></div>
        </div>
        <div class="row">
            <div>Amžius</div>
            <div><?= out($camp->age) ?> metai</div>
        </div>
    </div>
    </div>
</div>
