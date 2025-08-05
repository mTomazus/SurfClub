<div id="form-container"></div>

<?php if ($user->role === 'organizer') { ?>
    <div id="load-on" mx-get="competitions/create_comp" mx-target="#form-container" mx-select="#comp-table" mx-trigger="load"></div>
<?php } else { ?>
    <div id="load-on" mx-get="competitions/score_heat" mx-target="#form-container" mx-select="#form-container" mx-trigger="load"></div>
<?php } ?>

<script>
    function updateRangeSliderBackground() {
        var rangeInput = document.getElementById('score_range');
        if (!rangeInput) return;
        var value = rangeInput.value;
        var min = rangeInput.min ? rangeInput.min : 0;
        var max = rangeInput.max ? rangeInput.max : 10;
        var percent = ((value - min) / (max - min)) * 100;
        var color = 'linear-gradient(to right, rgb(0, 247, 148) ' + percent + '%, rgb(63, 63, 63) ' + percent + '%)';
        rangeInput.style.background = color;
    }

    function rangeSlider(value) {
        document.getElementById('score_value').innerText = value;
        updateRangeSliderBackground();
    }

    document.addEventListener('DOMContentLoaded', function() {
        var rangeInput = document.getElementById('score_range');
        if (rangeInput) {
            updateRangeSliderBackground();
            rangeInput.addEventListener('input', function() {
                rangeSlider(this.value);
            });
            // Add touch event listeners for better mobile support
            rangeInput.addEventListener('touchstart', function() {
                updateRangeSliderBackground();
            });
            rangeInput.addEventListener('touchmove', function() {
                updateRangeSliderBackground();
            });
            rangeInput.addEventListener('touchend', function() {
                updateRangeSliderBackground();
            });
        }
    });
    (function countdownTimerWatcher() {
    function updateCountdown() {
        const timerEl = document.querySelector('[data-end-time]');
        if (timerEl) {
            const endTimeStr = timerEl.getAttribute('data-end-time');
            const endTime = new Date(endTimeStr.replace(' ', 'T'));
            const now = new Date();
            let diff = Math.floor((endTime - now) / 1000);
            if (diff > 0) {
                const minutes = String(Math.floor(diff / 60)).padStart(2, '0');
                const seconds = String(diff % 60).padStart(2, '0');
                timerEl.textContent = `${minutes}:${seconds}`;
            } else {
                timerEl.textContent = "00:00";
            }
        }
    }
    setInterval(updateCountdown, 500);
    updateCountdown();
})();
</script>
<style>
    .box {
        padding: 1rem;
        background: white;
        display: grid;
        grid-template-columns: 1fr 100px;
        border-radius: 1rem;
        overflow: hidden;
        justify-content: center;
        align-items: center;
        & span {
            padding-left: 1rem;
            font-size: 3rem;
            color: black;
        }
        & input[type="range"] {
            background: transparent;
            -webkit-appearance: none;
        }
    }
    #score_range {
        -webkit-appearance: none;
        width: 100%;
        height: 45px;
        border:none;
        margin:auto;
        background: linear-gradient(to right,rgb(0, 247, 148) 50%,rgb(63, 63, 63) 50%);
        outline: none;
        opacity: 1;
        -webkit-transition: .2s;
        transition: opacity .2s;
        touch-action: pan-x;
    }

    #score_range::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        outline: none;
        box-shadow: none;
        width: 47px;
        height: 47px;
        background:rgb(0, 247, 148);
        cursor: pointer;
    }

    #score_range::-webkit-slider-runnable-track {
        -webkit-appearance: none;
        appearance: none;
        outline: none;
        border:none;
        box-shadow: none;
        height: 40px;
        background: transparent;
        cursor: pointer;
    }

    #score_range::-moz-range-thumb {
        appearance: none;
        width: 45px;
        height: 55px;
        background: #04AA6D;
        cursor: pointer;
    }
</style>