<div class="wrapper container-xxl">
    <h1 class="title">SURF PAMOKOS</h1>
    <h2>Išbandykite banglentes</h2>
    <a class="lg button mb-1" style="font-family:inherit;" href="lessons-schedules"><i class="fa fa-calendar mr-1" aria-hidden="true"></i>REGISTACIJA</a>
    <h3 class="mb-1">Puikiai išmanantys banglenčių sportą instruktoriai per pusantros valandos trūkmės pamoką pamokys, kaip pasigauti savo pirmą bangą. 
        Vedamos trejų lygių pamokos:</h3>
    <div class="content">
        <div class="lygiai mb-1">
            <div class="col bg-white box-shadow">
                <h2>pirmas lygis</h2>
                <h4>pradinis, išmoksite atsistoti ir išstovėti, 
                    saugumo bei etiketo taisykles vandenyje. Garantuojam daug gerų 
                    emocijų ir linksmai praleisto laiko</h4>
            </div>
            <div class="col bg-white box-shadow">
                <h2>antras lygis</h2>
                <h4>sudėtingiau, išsiaiškinsime apie bangas ir 
                    kaip jos veikia. Mokysimes laiku pastebėti ir pagauti, bei pasukti 
                    banglentę frontsaidu ir backsaidu</h4>
            </div>
            <div class="col bg-white box-shadow">
                <h2>trečias lygis</h2>
                <h4>nori jaustis puikiai lainupe. Orentuotis 
                    pozicionavime. Sunki ir sekinanti, bet tuo pačiu ir daug adrenalino 
                    suteikianti bus ši treniruotė</h4>
            </div>
        </div>

        <div class="tipai" style="min-height: 100%;display:grid;">
            <a href="lessons-schedules" class="bg-white mb-1 p-05 box-shadow">
                <div class="pamoka"><h2>PAMOKŲ PAKETAS</h2><h4>150€</h4></div>
                <p class="">Pakete dvi 1h trukmės pamokos, pirmoje pamokoje ramiame vandenyje išmokstam irklavimo, atsistojimo techniką ir balanso ant lentos. Antroje pamokoje jau bangose - pozicionavimą ir technikos tolimesnis įsisavinimas praktikoje.</p>
                <i class="fa fa-hand-pointer-o" aria-hidden="true"> registuokis</i>
            </a>
            <a href="lessons-schedules" class="bg-white mb-1 p-05 box-shadow">
                <div class="pamoka">
                    <h2>PRIVATI PAMOKA</h2>
                    <h4>85€</h4>
                </div>
                <p>Per šią pusantros valandos privačią serfingo pamoką pristatysime Jums etiketo, saugumo, serfingo pagrindus, irklavimo techniką, pozicionavimą bangose bei atsistojimo būdus.</p>
                <i class="fa fa-hand-pointer-o" aria-hidden="true"> registuokis</i>
            </a>
            <a href="lessons-schedules" class="bg-white mb-1 p-05 box-shadow">
                <div class="pamoka">
                    <h2>GRUPINĖ</h2>
                    <h4>40€</h4>
                </div>
                <p>Idealiai tinka pradedantiesiems ar tiems, kurie nori prisiminti pagrindus ir techniką. Pamoka kartu su mumis reiškia, kad mokotės iš geriausių, kad galėtumėte tapti geriausiais.</p>
                <i class="fa fa-hand-pointer-o" aria-hidden="true"> registuokis</i>
            </a>
            <a href="lessons-schedules" class="bg-white mb-1 p-05 box-shadow">
                <div class="pamoka">
                    <h2>PAMOKA DVIEM</h2>
                    <h4>120€</h4>
                </div>
                <p>Geriausiai tinka 2 asmenims - draugams ar draugėms, poroms ar artimiesiems. Čia mes pristatysime Jums saugumo ir serfingo pagrindus, irklavimo techniką bei atsistojimo būdus.</p>
                <i class="fa fa-hand-pointer-o" aria-hidden="true"> registuokis</i>
            </a>
            <a href="lessons-schedules" class="bg-white p-05 box-shadow">
                <div class="pamoka">
                    <h2>INDIVIDUALI PLUS</h2>
                    <h4>100€</h4>
                </div>
                <p>Ši serfingo pamoka tinka tiek naujokams tiek pažengusiems ir su orientuocija į pastioviai daromas esmines klaidas ir kilstelėtų Jūsų techniką į naują lygį. Jei ieškote vertos pamokos - Jūs ją radote.</p>
                <i class="fa fa-hand-pointer-o" aria-hidden="true"> registuokis</i>
            </a>
        </div>

        <div class="galerija">
            <h2 class="box-shadow">GALERIJA</h2>
            <div class="carousel-container mt-1 box-shadow">
                <button class="prev" onclick="changeSlide(-1)">&#10094;</button>
                <div class="carousel">
                    <?php
                        foreach ($images as $image) {
                            $imagePath = $path.$image;
                            echo "<img class='carousel-image' src='$imagePath' alt='$image'>";  
                        }
                    ?>
                </div>
                <button class="next" onclick="changeSlide(1)">&#10095;</button>
            </div>
        </div>

        <div class="kuponai">
            <h2 class="box-shadow">Dovanų Kuponai</h2>
            <div class="kuponas description mt-1 bg-white p-1 box-shadow">    
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" style="opacity: 1; transform: translate(0px, 0px);float: right;"><path d="M12 6.76l1.379 4.246h4.465l-3.612 2.625 1.379 4.246-3.611-2.625-3.612 2.625 1.379-4.246-3.612-2.625h4.465l1.38-4.246zm0-6.472l-2.833 8.718h-9.167l7.416 5.389-2.833 8.718 7.417-5.388 7.416 5.388-2.833-8.718 7.417-5.389h-9.167l-2.833-8.718z"></path></svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" style="opacity: 1; transform: translate(0px, 0px);float: right;"><path d="M12 6.76l1.379 4.246h4.465l-3.612 2.625 1.379 4.246-3.611-2.625-3.612 2.625 1.379-4.246-3.612-2.625h4.465l1.38-4.246zm0-6.472l-2.833 8.718h-9.167l7.416 5.389-2.833 8.718 7.417-5.388 7.416 5.388-2.833-8.718 7.417-5.389h-9.167l-2.833-8.718z"></path></svg>
                <h3>DOVANŲ KUPONAI</h3>
                <h3>nuo 40€</h3>
                <h3>Galioja metus, puiki dovana draugui ar kolegai. Galimi įvairus variantai, nuo grupinės pamokos iki individualios ar net kelių pamokų paketų.</h3>
                <h3>Susisiekiate su mumis, aptariame sąlygas, padarote pavedimą į mūsų sąskaitą ir gaunate kuponą pdf arba atspauzdintą.</h3>
            </div>
        </div>

        <div>
            <h2 class="box-shadow">KOMANDOS FORMAVIMAS</h2>
            <div class="kuponas description mt-1 bg-white p-1 box-shadow">
                <h3>nuo 200€</h3>
                <p style="color:rgb(69, 69, 69)">Subūrėte didelę grupę nuo 6 iki 12 žmonių, kuri norėtų išbandyti save bangose? Pabūti kartu, patirti geras emocijas bei palaikyti vienas kitą? Tai tinkamiausias pasirinkimas jums!</p>
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
    h4 {
        color:var(--dark-text);
        margin: 0.5rem;
    }
    .content {
        margin-top: 1rem;
        display:grid;
        gap:1rem;
        font-family: alegreya;
        h2 {
        background-image: linear-gradient(to right, #2f78a8, #114161);
        min-width: 100%;
        margin:0;
        }
    }
    .content p::first-letter {
        font-size:150%;
     }
    .tipai {
        grid-area: tipa;
        p {
            color:var(--dark-text);
            padding: 1rem;
            margin: 0;
            font-size: 1em;
            line-height: 1.5rem;
            text-align: left;
            font-family: alegreya;
            text-shadow: none;
        }
        a {
            position: relative;
            text-decoration: none;
            color: var(--dark-text);
        }
        a:hover {
            box-shadow: 0 0 10px var(--banglente);
            transform: scale(1.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        i {
            position: absolute;
            right: 1rem;
            bottom: 1rem;
        }
    }
    .col {
        padding:0.5rem;
        min-width:360px;
    }
    .galerija {
        grid-area: gale;
    }
    .registracija {
        grid-area: regi;
    }
    .lygiai {
        grid-area: lygi;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        overflow-x: scroll;
        scroll-behavior: smooth;
        h4 {
            color:var(--dark-text);
            padding: 1em;
            margin: 0;
            font-size: 1em;
            line-height: 1.5em;
            text-align: left;
            font-family: alegreya;
            text-shadow: none;
        }
    }
    .koponai {
        grid-area: kupo;
    }
    .registracija {
        grid-area: regi;
    }
    .kuponas h3 {
        font-size: 1rem;
        padding: 0 1rem;
        color:var(--dark-text);
    }
    .pamoka {
        display: flex;
        width: 100%;
        position: relative;
        align-items: center;
        h2 {
            color:white;
            text-decoration:none;
            text-align: left;
            padding-left: 2rem;
            background: linear-gradient(161deg, #134567 0%, #256690 20%, rgba(47, 120, 168, 0.37) 42%, rgba(0, 212, 255, 0) 65%, rgba(0, 212, 255, 0) 100%);
        }
        h4 {
            position: absolute;
            right: 1rem;
            font-size:1.2em;
        }
    }
    .bg-white {
        background: white;
    }
    .box-shadow {
        box-shadow: 0 0.2rem 0.5rem #00000075;
    }
    .p-05 {
        padding:0.5rem;
    }
    .p-1 {
        padding:1rem;
    }
    .gap-1 {
        gap:1rem;
    }
    .d-grid {
        display:grid;
    }
    .m-auto {
        margin: auto;
    }
    .m-0 {
        margin: 0;
    }
    .carousel-container {
            text-align: center;
            position: relative;
            max-width: 600px;
            margin: 0 auto;
            overflow: hidden;
        }
        .carousel {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }
        .carousel-image {
            min-width: 100%;
            max-width: 100%;
            height: auto;
        }
        button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0,0,0,0.5);
            color: white;
            border: none;
            padding: 10px;
            margin:0;
            cursor: pointer;
            font-size: 18px;
            z-index:2;
        }
        button:hover {
            background-color: rgba(0,0,0,0.8);
        }
        .prev {
            left: 10px;
        }
        .next {
            right: 10px;
        }

        .lygiai::-webkit-scrollbar {
            display: none;
        }

/* Hide scrollbar for IE, Edge and Firefox */
        .lygiai {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }


    @media only screen and (max-width: 575px) {
        .content { 
            display:flex;
            flex-direction:column;
        }
        .lygiai {
            display: flex;
        }
        .tipai {
            p {
                padding: 0.5em;
                font-size: 0.9em;
                margin: 0;
                text-align: justify;
            }
        }
        .pamoka {
            h4 {
                right: 0.5em;
                font-size: 1.3rem;
            }
        }
        button {
            width: auto;
        }
    }
    @media only screen and (min-width: 576px) and (max-width: 873px) {
        .content { 
            display:flex;
            flex-direction:column;
        }
        .lygiai {
            display: flex;
            flex-direction: row;
        }
        .tipai {
            p {
                padding: 0.5em;
                font-size: 0.9em;
                margin: 0;
                text-align: justify;
            }
        }
        .pamoka {
            h4 {
                right: 0.5em;
                font-size: 1.3rem;
            }
        }
        button {
            width: auto;
        }
    }
    @media only screen and (min-width:874px) and (max-width: 1198px) {
        .content { 
            grid-template-columns: repeat(2, 1fr);
            grid-template-areas:
                "lygi lygi"
                "tipa tipa"
                "gale gale"
                "kupo regi";
        }
    }   
    @media (min-width: 1199px) {
        .content { 
            grid-template-columns: repeat(3, 1fr);
            grid-template-areas:
                "lygi lygi lygi"
                "tipa tipa gale"
                "tipa tipa kupo"
                "tipa tipa regi";
        }
    }
</style>