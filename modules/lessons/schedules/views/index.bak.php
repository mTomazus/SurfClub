<div class="container">
    <?php
        echo '<h2 class="text-center">Artimiausiu metu pagal esamas prognozes planuojamos pamokos</h2>';
        echo '<div class="section">';
        foreach($rows as $row) {
            if ($row->available_places !== $row->reserved_places
                && $row->date >= date('Y-m-d')) {
                $places_left = $row->available_places - $row->reserved_places;
                ?>
                    <div class="thumbhead button" mx-get="lessons-schedules/checkout_form/<?= $row->id ?>" 
                                                mx-select="#form" 
                                                mx-build-modal='{
                                                    "id": "checkout-modal" }'>
                        <h4><?= $row->name ?></h4>
                        <h5><strong><?= $row->price ?> €</strong></h5>
                        <p style="margin-top:1em"><?= $row->date ?></p>
                        <p><?= $row->start_time ?></p>
                        <p>Laisvų vietų: <?= $places_left ?></p>
                    </div>
                <?php
            } 
        }
        echo '</div>';
    ?>
</div>
<style>
    h2 {
        color: whitesmoke;
        border-bottom: 2px solid;
    }
    .section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    }
    .button {
        color: black;
    }
    .thumbhead {
        display: flex;
        flex-direction:column;
        height:100px;
        width:100px!important;
        background-color:lightskyblue;
        margin:1rem;
        h4 {
            font-size: 0.8em;
            margin: 0.5em auto 0;
        }
        h5 {
            font-size: 0.7em;
            margin: 0 auto;
        }
        p {
            margin:auto;
            font-size: 0.7em;
        }
        p:last-child {
            margin: 2em auto 3em;
            font-weight: 900;
        }
    }
</style>
