<div class="wrapper container-xxl">
    <section id="logo-icon">
        <img src="images/logo.png" alt="logo surf club">
    </section>
    <section class="content">
        <div class="content-cards">
            <div class="rekvizitai">
                <p>VšĮ Banglentė</p>
                <p>Vėtros g. 8, Klaipėda</p>
                <p>Lietuva</p>
                <p>Įmonės kodas - 304105037</p>
                <p>Swed bank: LT227300010144594068</p>
                <p><a href="tel:+37068602356">+370 686 02356</a></p>
                <p><a href="mailto:info@surfclub.lt" style="text-transform: lowercase;">info@surfclub.lt</a></p>
                <div id="social">
                    <a href="https://m.me/banglente" class="button messenger">
                        <img src="images/messenger.png" alt="messenger icon">
                    </a>
                    <a href="" class="button whatsapp">
                        <img src="images/whatsapp.png" alt="whatsapp icon">
                    </a>
                </div>
            </div>
            <div class="apie">
                <a href=komanda>
                    <p>KOMANDA</p>
                    <img src="images/kontaktai/komanda.jpeg" alt="komanda">
                </a>
                <a href=ataskaitos>
                    <p>ATASKAITOS</p>
                    <img src="images/kontaktai/ataskaitos.jpeg" alt="ataskaitos">
                </a>
                <a href=karjera>
                    <p>KARJERA</p>
                    <img src="images/kontaktai/karjera.jpeg" alt="karjera">
                </a>
                <a href=parama>
                    <p>PARAMA</p>
                    <img src="images/kontaktai/parama.jpeg" alt="parama">
                </a>
            </div>
            <div class="c_forma">
                <h2>KLAUSKITE</h2>
                <div mx-get="enquiries/index" 
                    mx-trigger="load"
                    mx-target="this" 
                    mx-select="#contact-form"
                    mx-on-success="#contact-form"></div>
            </div>
        </div>
    </section>
    <section id="remejai">
        <h2 class="mb-1 text-center text-light">REMĖJAI / DRAUGAI</h2>
        <div class="item item1">
            <img class="my-0 shadow-none" style="max-width:100%" src="images/decathlon.svg" alt="decathlon logo">
        </div>
        <div class="item item2">
            <img class="my-0 shadow-none" style="max-width:100%" src="images/kontaktai/evadenta.png" alt="evadenta logo">
        </div>
        <div class="item item3">
            <img class="my-0 shadow-none" style="max-width:100%" src="images/klaipeda.diena.png" alt="klaipeda logo">
        </div>
        <div class="item item4">
            <img class="my-0 shadow-none" style="max-width:100%" src="images/kontaktai/oneill.png" alt="oneill logo">
        </div>
        <div class="item item5">
            <img class="my-0 shadow-none" style="max-width:100%; margin:auto;" src="images/kontaktai/klaipeda.svg" alt="savivaldybe logo">
        </div>
        <div class="item item6">
            <img class="my-0 shadow-none" style="max-width:100%; margin:auto;" src="images/kontaktai/ripcurl.svg" alt="ripcurl logo">
        </div>
        <div class="item item7">
            <img class="my-0 shadow-none" style="max-width:100%; margin:auto;" src="https://www.scaladream.com/wp-content/uploads/2024/08/scala-dream-logo-svg.svg" alt="scaladream logo">
        </div>
    </section>
    <section>
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d16267.742706037403!2d21.06580763110815!3d55.73128473477712!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46e4d98251ef4133%3A0x6ba6022d59fd57f2!2sBanglentes!5e1!3m2!1slt!2slt!4v1726394268519!5m2!1slt!2slt" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </section>
    <section class="foot">
        <a href="welcome/politika">Privatumo politika</a>
        <a href="welcome/taisykles">Taisyklės</a>
        <a href="welcome/grazinimas">Grąžinimo sąlygos</a>
    </section>
</div>

<style>

    h1, h2 {
        color: var(--clr-light);
        font-family: "Source Sans Pro", sans-serif;
        text-align: center;
        font-weight: 900;
        text-shadow: 0 0.2rem 0.5rem #00000075;
    }
    h2 {
        border-bottom: 1px solid var(--clr-light);
    }
    input[type="text"], 
    input[type="search"], 
    input[type="number"], 
    input[type="email"], 
    input[type="password"], 
    button, textarea, select, .button {
        border-radius: 20px;
        margin-bottom: 0.5rem;
    }
    a:hover {
        transform:scale(0.9);
        transition: all 0.5s;
    }
    input:hover, button:hover, textarea:hover {
        border: 1px solid var(--clr-dark);
        box-shadow: 0px 0px 5px white;
        transition: all 0.5s;
    }
    button + p {
        display:none;
    }
    .flash-msg {
        display: inline;
        width: 60%;
        padding: 0.5rem 3rem;
        border-radius: 20px;
        margin: auto 1rem;
    }
    .flash-msg + p {
        display:none;
    }
    .content-cards {
            display: grid;
            gap:1rem;
            padding:0;
            & a {
                text-decoration: none;
                color: var(--clr-light);
                font-size: 1.5rem;
            }
        }
    .content {
        margin-top: 4rem;
        display:grid;
        gap:1rem;
        & h2 {
        color: var(--clr-light);
        padding: 1rem 0;
        text-align: center;
        min-width: 100%;
        margin:0;
        }
        & p {
        text-transform: uppercase;
        text-align:center;
        color: var(--clr-light);
        text-shadow: 1px 1px 1px slategrey;
        font-weight: 900;
        line-height: 1em;
        }
    }
    .rekvizitai {
        alighn-self: center;
        & p:first-child {
            font-size:2rem;
        }
        & p:has(a) {
            margin:2rem;
        }
        & a:hover {
            color: #c4dac9;
            transition:1s;
        }
    }
    .social-links {
        margin-top: 1rem;
        display: flex;
        justify-content: space-evenly;
    }
    .contact {
        margin-top:1rem;
        & input {
            margin-bottom:1rem;
        }
        & p {
			background: aquamarine;
			padding: 0.5rem;
		}
    }
    #social {
        display:flex;
        gap:1rem;
        justify-content: center;
    }
    #social img {
            filter: invert();
    }
    .messenger {
        background-color:#00c6ff; 
        padding: 0;
        margin: 0 0 1rem;
        height: 45px;
        max-width: 45px;
    }
    .whatsapp {
        background-color:#25d366;  
        padding: 0;
        margin: 0 0 1rem;
        height: 45px;
        max-width: 45px;
    }
    .messenger:hover {
        background-color:#0078ff;  
    }
    .whatsapp:hover {
        background-color:#128c7e;  
    }
    #logo-icon {
        display:none;
    }
    #remejai {
        width: 90%;
        max-width: 1532px;
        height:155px;
        margin:4rem auto;
        position:relative;
        overflow:hidden;
        mask-image: linear-gradient(
            to right,
            rgb(0,0,0,0),
            rgb(0,0,0,1) 20%,
            rgb(0,0,0,1) 80%,
            rgb(0,0,0,0)
        );
    }
    .item {
        position:absolute;
        display:grid;
        align-items:center;
        left:calc(200px * 7);
        border-radius:5px;
        width: 200px;
        height:100px;
        animation-name: scrollleft;
        animation-duration: 25s;
        animation-timing-function: linear;
        animation-iteration-count: infinite;
    }
    @keyframes scrollleft {
        to {
            left:-200px;
        }
    }
    .item1 {
        animation-delay: calc(25s / 7 * (7 - 1) * -1);
    }
    .item2 {
        animation-delay: calc(25s / 7 * (7 - 2) * -1);
    }
    .item3 {
        animation-delay: calc(25s / 7 * (7 - 3) * -1);
    }
    .item4 {
        animation-delay: calc(25s / 7 * (7 - 4) * -1);
    }
    .item5 {
        animation-delay: calc(25s / 7 * (7 - 5) * -1);
    }
    .item6 {
        animation-delay: calc(25s / 7 * (7 - 6) * -1);
    }
    .item7 {
        animation-delay: calc(25s / 7 * (7 - 7) * -1);
    }
    .text-light {
        color: whitesmoke;
    }
    .foot {
        margin: 1rem;
        padding: 0.5rem;
        color: white;
        display: flex;
        justify-content: space-around;
        & a {
            color:white;
            font-weight:900;
        }
    }
    .apie {
        display: flex;
        flex-direction: column;
        & a {
            flex-grow:1;
            display: grid;
            position:relative;
            grid-template-columns: 2fr 1fr;
            justify-items: center;
            align-items: center;
            padding: 0.3rem 0;
            transition: all 0.5s ease;
            border: 1px solid var(--clr-light);
            border-radius: 5rem;
        }
        & p {
            padding: 0;
            margin: 0;
            text-shadow: 2px 2px 5px grey;
        }
        & img {
            height: 70px;
            border-radius: 15px;
            box-shadow: 1px 1px 5px grey;
        }
    }
    @media only screen and (max-width: 575px) {
        #logo-icon {
            display: grid;
            justify-items: center;
            filter: drop-shadow(0 0 5px grey);
        }
        .content {
            margin-top: 0rem;
        }
        #remejai {
            margin: 1rem auto;
        }
    }
    @media only screen and (min-width: 576px) and (max-width: 873px) {

    }
    @media only screen and (min-width:874px) and (max-width: 1198px) {
        .content-cards { 
            grid-template-columns: 1fr 1fr 1fr;
        }
    }   
    @media (min-width: 1199px) {
        .content-cards { 
            grid-template-columns: 1fr 1fr 1fr;
        }
    }

</style>