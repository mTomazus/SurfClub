<h1 class="title">ĮRANGOS NUOMA</h1>
<div class="d-grid nuoma">
    <div class="card">
        <img src="images/nuoma/bangle-sm.jpeg" alt="banglentes surfboards">
        <div class="d-grid">
            <h3>BANGLENTĖ</h3>
            <h4>nuo 15€ - dvi valandos</h4>
            <h4>40€ - diena</h4>
            <p class="text-dark font-weight-normal">Mes visada jums padėsime išsirinkti tinkamą banglentę, patarsime kur plaukti. Ne vienerius metus daugelyje pasaulio vietų skrieje banga patarsime ir kitais klausimais. Nesidrovėkite klausti.
        </p></div>   
    </div>
    <div class="card">
        <img src="images/nuoma/sup-sm.jpeg" alt="irklentes standup paddle">
        <div class="d-grid">
            <h3>IRKLENTĖ</h3>
            <h4>15€ - valanda</h4>
            <h4>40€ - diena</h4>
            <p class="text-dark font-weight-normal">Kietos įvairaus dydžio irklentės puikiausiai tinka pasiplaukioti jūroje. Gera viso kūno treniruotė su irklente idealiai tiks bet kokioo amžiaus sportuojančiam.
        </p></div>
    </div>
    <div class="card">
        <img src="images/nuoma/hidro-sm.jpeg" alt="hidrokostiumai wetsuits">
        <div class="d-grid">
            <h3>HIDROKOSTIUMAS</h3>
            <h4>10€ - dvi valandos</h4>
            <h4>20€ - diena</h4>
            <p class="text-dark font-weight-normal">Įvairių dydžių, šilti 5/4 mm. vyriški, moteriški ir vaikiški hidrokostiumų nuoma. Vandens temparatūrai pasiekus bent 13 laipsnių su 5mm. hidrokostiumu, batais ir kapišonu visiškai nešalta
        </p></div>
    </div>
    <div class="card">
        <img src="images/nuoma/skate-sm.jpeg" alt="riedlentes skateboards">
        <div class="d-grid">
            <h3>RIEDLENTĖ</h3>
            <h4>15€ - dvi valandos</h4>
            <h4>30€ - diena</h4>
            <p class="text-dark font-weight-normal">Surf skeitai - puiki pramoga ir labai gera banglenčių treniruočių priemonė. Turime visų populiariausių brandų surf riedlentes - Carver, SmoothStar ir Miller. Būtinai išmėginkite!
        </p></div>
    </div>
    <div class="card">
        <img src="images/nuoma/skim-sm.jpeg" alt="skimboards">
        <div class="d-grid">
            <h3>SKIM BOARDAS</h3>
            <h4>10€ - dvi valandos</h4>
            <h4>20€ - diena</h4>
            <p class="text-dark font-weight-normal">Mažesnė nei banglentė, neturi pelekų ir skirta čiuožti negiliu vandeniu. Viskas prasideda paplūdimyje, metant lntą ant vandens :)
        </p></div>
    </div>
    <div class="card">
        <img src="images/nuoma/body-sm.jpeg" alt="puslentės bodyboard">
        <div class="d-grid">
            <h3>PUSLENTĖ</h3>
            <h4>10€ - dvi valandos</h4>
            <h4>25€ - diena</h4>
            <p class="text-dark font-weight-light">Puiki priemone suprasti bangų gaudymo mechaniką ir čiuožimo nuo bangos dinamiką.
        </p></div>
    </div>

<style>
    :root {
        --txt-dark: #454545;
        --txt-white: white;
        --bg-white:white;
        --banglente: #2f78a8;
        --banglente-light: #114161;
    }
    body {
        font-family: "Source Sans Pro", sans-serif;
        text-align: center;
        font-weight: 900;
        margin: auto;
        text-shadow: 0 0.2rem 0.5rem #00000075;
        padding: 0;
        color: var(--txt-white);
        font-size: 1rem;
    }
    .title {
        margin:1rem 0;
    }
    h1, h2, h3 {
        text-align: center;
        text-transform: uppercase;
    }
    .card {
        display:flex;
        flex-direction:column;
        background:var(--bg-white);
        color:var(--txt-dark)
    }
    .card > div {
        padding: 1rem;
        & h3 {
            font-size: max(2rem, 2vw);
            margin-bottom:1rem;
        }
        & h4 {
            font-size: max(1.5rem, 1.5vw);
            margin:0;
        }
        & p {
            font-size: max(1rem, 1.5vw);
        }
    }
    .nuoma {
        grid-template-columns: 1fr 1fr 1fr;
        width:90%;
        margin:auto;
        gap:2rem;
    }
    .d-grid {
        display:grid;
    }
    @media (max-width: 576px) {
        .nuoma {
            grid-template-columns: 1fr;
            gap:1rem;
        }
    }
    @media (min-width: 577px) {
        .nuoma {
            grid-template-columns: 1fr 1fr;
            gap:2rem;
        }
    }   
    @media (min-width: 992px) {

    }
    @media (min-width: 1200px) {
        .nuoma {
            grid-template-columns: 1fr 1fr 1fr;
            gap:2rem;
        }
    }
</style>