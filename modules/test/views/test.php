<h1>hello</h1>

<h2>Your token is: <strong><?= $token ?></strong></h2>

<p class="xl"><button onclick="sendRequest()">HTTP request NOW</button></p>
<p>Server response:</p>
<div id="server-response">
    <p>HTTP Response Code:<span id="response-code"></span></p>
    <p>HTTP Response Text:<span id="response-text"></span></p>
</div>

<style>
    #server-response {
        background-color: white;
        color: black;
        border: 2px solid red;
        padding:1rem;
    }
    #response-text, #response-code {
        font-weight:900;
    }
</style>

<script>
    function sendRequest() {
        const http = new XMLHttpRequest();
        const targetUrl = 'api/get/lesson_schedules';
        http.open('get', targetUrl);
        http.setRequestHeader('trongateToken', '<?= $token ?>');
        http.send();
        http.onload = function() {
            // populate response code
            const responseCodeEl = document.querySelector("#response-code");
            responseCodeEl.innerText = http.status;
            // populate response code
            const responseTextEl = document.querySelector("#response-text");
            responseTextEl.innerText = http.responseText;
        }
    }
</script>