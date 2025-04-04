<style>
    h1 {
        color:white;
        text-align:center;
    }
    .card {
        margin: 1rem;
        padding: 1rem;
        background: white;
        max-width: 350px;
        & h3 {
            color:black;
        }
    }
    * {
        box-sizing: border-box;
    }
    p {
        margin-top:0;
    }
    table {
        background:white;
        box-shadow: 0 0 5px white;
        td {
            border:none;
            text-transform: uppercase;
            font-size: 0.7rem;
        }
    }
    tr td:first-child {
        font-size-adjust: 0.5;
    }
    .wrapper {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }
    .jersey {
        width: 30px;
        height: 30px;
        border-radius: 15px;
        margin:auto;
    }
    .white {
        background: white;
        border: 1px solid black;
        box-shadow: 0 0 5px white;
    }
    .red {
        background: red;
        color:white;
        border: 1px solid black;
        box-shadow: 0 0 5px red;
    }
    .green {
        background: green;
        color:white;
        border: 1px solid black;
        box-shadow: 0 0 5px green;
    }
    .blue {
        background: blue;
        color:white;
        border: 1px solid black;
        box-shadow: 0 0 5px blue;
    }
    .heat {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 5px;
        padding: 1rem;
        box-shadow: 0 0 10px white;
        & div {
            display: flex;
            justify-content: space-between;
            align-items: center;
            & p {
                margin: 0 2rem;
                font-weight: 900;
                font-family: Silom;
                transition:all 1s;
            }
            & p:hover {
                background:white;
            }
        }
    }
    tbody {
        font-weight: 900;
        font-family: Baskerville;
        text-align: center;
    }
    .nav {
        padding: 1rem 0;
        justify-content: center;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(75px, 1fr));
        & button {
            background: rgba(255, 255, 255, 0.30);
            box-shadow: 0 0 5px white;
            border: 0;
            border-radius: 0;
        }
        & button:hover{
            background: rgba(255, 255, 255, 0.60);
            box-shadow:0 0 5px skyblue;
        }
    }
</style>
<h1>competitions</h1>
<div class="">

<?php foreach ($competitions as $comp) {

    echo '<div class="card" mx-select="#full-draw" mx-target="h1" mx-swap="outerHTML" mx-get="competitions-heats/show_heats_draw/' . out($comp->id) . '"><h3>' . out($comp->name) . ' ' . out($comp->year) . '</h3>';
    echo '<h3>' . out($comp->location) . '</h3></div>';

}

?>
</div>