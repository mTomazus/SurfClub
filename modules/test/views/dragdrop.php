<section class="container">
    <div class="box">
        <div class="image" draggable="true"></div>
    </div>
    <div class="box"></div>
    <div class="box"></div>
    <div class="box"></div>
</section>

<style>
    main {
        padding:0;
        margin:0;
        box-sizing:border-box;
        display:flex;
        align-items:center;
        justify-content:center;
        height:100vh;

    }
    .container {
        display:flex;
        flex-wrap:wrap;
        gap:1rem;
        justify-content:center;
    }
    .container .box {
        position:relative;
        height:160px;
        width:160px;
        border-radius: 10px;
        border: 2px solid black;
    }
    .box .image {
        background-image: url("images/landing-2-600.jpg");
        background-size: cover;
        background-position:center;
        height:100%;
        width:100%;
        border-radius: 8px;
    }
    .box.hovered {
        border: 2px dashed white;
    }
</style>

<script>
    // DOM elements
    const boxes = document.querySelectorAll('.box'),
        image = document.querySelector('.image');
    
    // LOOP thou each box element
    boxes.forEach(box => {
        // then draggable element is dragged over the box element
        box.addEventListener("dragover", e => {
            e.preventDefault();
            box.classList.add("hovered");
        });
        // then draggable element leaves the box element
        box.addEventListener("dragleave", () => {
            box.classList.remove("hovered");
        });
        // then draggable element lis droped on the box element
        box.addEventListener("drop", () => {
            box.appendChild(image);
            box.classList.remove("hovered");
        });
    });


</script>