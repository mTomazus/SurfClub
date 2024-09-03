<h1 class="title">VARŽYBOS</h1>
        <div class="content">
            <div class="remejai">
                <h2>REMĖJAI</h2>
                <div class="remejai bg-white d-grid" style="grid-template-columns: 1fr 1fr; gap:1rem;">
                    <img class="my-0 shadow-none" style="max-width:100%" src="images/decathlon.svg" alt="decathlon logo">
                    <img class="my-0 shadow-none" style="max-width:100%" src="images/evadenta.png" alt="evadenta logo">
                    <img class="my-0 shadow-none" style="max-width:100%" src="images/oneill.jpeg" alt="oneill logo">                
                    <img class="my-0 shadow-none" style="max-width:100%" src="images/klaipeda.diena.png" alt="klaipeda logo">
                </div>
            </div>
            <div class="registracija bg-white">
                <h2>REGISTRACIJA</h2>
                <div class="bg-white">
                    
                </div>
            </div>

            <div class="skelbimas bg-white" style="gap:1rem;grid-template-columns:repeat(2, 1fr)">
                <h2>ARTIMIAUSIOS</h2>
                <div class="bg-white d-grid p-3">
                    <div class="p-1">
                        <img class="img" src="images/2024-cover.jpeg" alt="posteris varžybų">
                        <h3 class="text-dark">Molas Junior Surf Competition 2024</h1>
                        <h3 class="text-dark">2024 m. Liepa</h3>
                    </div>
                    <div class="ms-4">
                        <ul class="text-dark">
                            <li>Pogrupiai:
                                <ul class="text-dark">
                                    <li>U-14 - iki 14 metų (vaikinai / merginos);</li>
                                    <li>U-16 - iki 16 metų (vaikinai / merginos);</li>
                                    <li>U-18 - iki 18 metų (vaikinai / merginos).</li>
                                </ul>
                            </li>
                            <li>Mažiausiai 6 dalyviai pogrupyje,</li>
                            <li>Vertinimo kriterijai:
                                <ul class="text-dark">
                                    <li>Radikalūs kontroliuojamai atlikti manevrai;</li>
                                    <li>Kritinėje bangos dalyje;</li>
                                    <li>Lužusios bangos atvejais:
                                        <ul class="text-dark">
                                            <li>Taisyklingas atsistojimas;</li>
                                            <li>Bandymas pakeisti kryptį.</li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex justify-content-around text-center">
                        <a class="flex col-2" href="/incoming/Junior2022.pdf" target="_blank">
                            <img class="w-50 float-none m-0" style="box-shadow:none" src="/img/pdf.png" alt="pdfas">
                            <h5 class="text-dark">Nuostatai</h5>
                        </a>
                        <a class="flex col-2" href="/public/2024-tinklelis.pdf" target="_blank">
                            <img class="w-50 float-none m-0" style="box-shadow:none" src="/img/pdf.png" alt="pdfas">
                            <h5 class="text-dark">Tinklelis</h5>
                        </a>
                        <a class="flex col-2" href="/public/2024-junior.pdf" >
                            <img class="w-50 float-none m-0" style="box-shadow:none" src="/img/pdf.png" alt="pdfas">
                            <h5 class="text-dark">Info(en)</h5>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="dalyviai bg-white">
                <h2>DALYVIAI</h2>
                <div class="bg-white">
                    
                </div>
            </div>

        </div>

    </div>

<style>
    :root {
        --dark-text: #454545;
        --white-text: white;
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
        color: var(--white-text);
        font-size: 1rem;
    }
    h1, h2, h3 {
        text-align: center;
        text-transform: uppercase;

    }
    .content {
        margin-top: 1rem;
        display:grid;
        gap:1rem;
    }
    .content h2 {
        background-image: linear-gradient(to right, #2f78a8, #114161);
        min-width: 100%;
        margin:0;
    }
    .skelbimas li, h3 {
        color: var(--dark-text);
        margin:0;
    }
    .remejai {
        grid-area: reme;
    }
    .dalyviai {
        grid-area: daly;
    }
    .registracija {
        grid-area: regi;
    }
    .skelbimas {
        grid-area: skel;
    }
    .bg-white {
        background: white;
    }
    .p-1 {
        padding:1rem;
    }
    .d-grid {
        display:grid;
    }
    @media (min-width: 576px) {
        .content { 
            grid-template-columns: repeat(1, 1fr);
            grid-template-areas:
                "skel"
                "reme"
                "regi"
                "daly"; 
        }
    }
    @media (min-width: 768px) {
        .content { 
            grid-template-columns: repeat(2, 1fr);
            grid-template-areas:
                "skel skel"
                "reme reme"
                "regi daly"; 
        } 
    }   
    @media (min-width: 992px) {
        .content { 
            grid-template-columns: repeat(3, 1fr);
            grid-template-areas:
                "skel skel regi"
                "reme reme daly";
        } 
    }
    @media (min-width: 1200px) {
        .content { 
            grid-template-columns: repeat(5, 1fr);
            grid-template-areas:
                "skel skel skel regi regi"
                "reme reme reme daly daly";
        }  
    }
</style>