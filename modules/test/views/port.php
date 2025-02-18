<div class="container-xxl">
    <h1>ORO SĄLYGOS</h1>
    <h3>Melnragė</h3>
    <div class="container d-flex flex-column">
        <div class="server-response">
            <h2>Vėjo greitis</h2>
            <p><span id="result1"></span><span class="speed"> M/S</span></p>
        </div>

        <div class="server-response">
            <h2>Vėjo kryptis</h2>
            <p><span id="result2"></span><span class="speed"> °</span></p>
        </div>

        <div class="server-response">
            <h2>Temperatūra</h2>
            <p><span id="result3"></span><span class="speed"> °C</span></p>
        </div>
    </div>
</div>


<style>
    h1, h2, h3, span {
        color:white;
        text-transform: uppercase;
        font-weight:900;
    }

    .server-response {
        padding:1rem;
        margin:1.5rem auto;
        text-align:center;
    }
    h2 {
        text-shadow: 1px 1px 10px var(--primary);
        text-align:center;
    }
    p span {
        text-shadow: 1px 1px 10px var(--primary);
        font-size-adjust: 1.5;
    }
    #response-text-1 {
        padding: 0.5rem;
    }
    .speed {
        padding: 0.5rem 0.5rem 0.5rem 0;
    }

</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var index = ["wind_speed", "wind_direction", "air_temparature_first"];

        for (var i = 0; i < index.length; i++) {

            const http = new XMLHttpRequest();
            const targetUrl = 'https://portofklaipeda.lt/wp-json/api/meteo_data?method=' + index[i];
            const responseTextEl = document.getElementById(`result${i + 1}`);

            http.open('get', targetUrl);
            http.send();

            http.onload = function() {
                // populate response text
                var data = JSON.parse(http.responseText);
                var dataNew = data[data.length - 1][1];
                dataNew = parseFloat(dataNew).toFixed(2);
                responseTextEl.innerText = dataNew;
            }
        }
    });
</script>