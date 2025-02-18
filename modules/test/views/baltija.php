<h1>Baltijos vandens temperatūra</h1>

<p class="xl"><button onclick="sendRequest()">temperatūra</button></p>
<div id="server-response">
    <p><span id="response-text"></span></p>
</div>

<style>
    #server-response {
        background-color: white;
        color: black;
        border: 2px solid red;
        padding:1rem;
    }
    #response-text {
        font-weight:900;
    }
</style>

<script>
    function sendRequest() {

        console.log(fetch('https://api.meteo.lt/v1/hydro-stations/klaipedos-vms/observations/measured/latest'))
            .then(res => console.log(res))

        const http = new XMLHttpRequest();
        const targetUrl = 'https://api.meteo.lt/v1/hydro-stations/klaipedos-vms/observations/measured/latest';
        http.open('get', targetUrl);
        http.send();
        http.onload = function() {
            // populate response text
            const responseTextEl = document.querySelector("#response-text");
            responseTextEl.innerText = http.responseText;
        }
    }
</script>