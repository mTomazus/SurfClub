<h1><?= $headline ?></h1>
<?php
flashdata();
echo '<p>'.anchor('news/create', 'Create New News Record', array("class" => "button")).'</p>'; 

if (count($rows)>0) { 
    echo Pagination::display($pagination_data);
    ?>
    <div class="newswire">
        <div class="newswire-search-results">
            <?php
            foreach($rows as $row) {
            ?>
            <div>
                <div class="timestamp">
                    <span class="newswire-highlight">&#9679;</span> 
                    <?= date('jS M Y', strtotime($row->date_and_time)) ?>
                </div>
                <div>
                    <h3><?= anchor($row->article_url, $row->article_headline) ?></h3>
                </div>
                <div class="article-body"><?= $row->article_body ?></div>
            </div>
            <?php
            }
            ?>
        </div>
    </div>

<?php
} else {
    echo '<p>Your search produced no results.</p>';
}
?>

<link rel="stylesheet" href="news_module/css/newswire.css">

<script>
var allBtns = document.getElementsByClassName("button");
var createUrl = '<?= BASE_URL ?>news/create';
var moduleHome = '<?= BASE_URL ?>news';
for(i=0; i<allBtns.length; i++) {
    if (allBtns[i]['href'] == createUrl) {
        allBtns[i]['href'] = moduleHome;
        allBtns[i].innerHTML = 'Go Back';
        allBtns[i].classList.add("alt");
    }
}
</script>