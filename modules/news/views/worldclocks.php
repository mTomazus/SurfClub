<div class="world-clocks" style="opacity: 0;">
    <div>
        <div>New York</div>
        <div id="NewYork"></div>
    </div>
    <div>
        <div>Vancouver</div>
        <div id="Vancouver"></div>
    </div>
    <div>
        <div>Melbourne</div>
        <div id="Melbourne"></div>
    </div>
    <div>
        <div>Hong Kong</div>
        <div id="HongKong"></div>
    </div>
    <div>
        <div>Paris</div>
        <div id="Paris"></div>
    </div>
    <div>
        <div>London</div>
        <div id="London"></div>
    </div>
</div>
<script src="news_module/js/worldclocks.js"></script>
<script>
setInterval(() => {
  worldClockZone();
}, 1000);

window.onload = function() {
  document.getElementsByClassName("world-clocks")[0].style.opacity = 1;
  document.getElementsByClassName("world-clocks")[0].style.transition = '3s';
  worldClockZone;
}
</script>