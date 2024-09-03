<?php
if (!isset($articles[0])) {
    echo '<p>Under construction (please check back later)</p>';
    return;
}
?>
<div class="newswire">
    <div class="newswire-header">
        <div>
            <h1 style='color:white;'>Naujienos</h1>
        </div>
        <div>
            <?php
            echo form_open('news/search/1/', array("method" => "get", "class" => "newswire-search-form"));
            $search_attr['id'] = 'searchphrase';
            $search_attr['placeholder'] = 'Search articles';
            $search_attr['autocomplete'] = 'off';
            echo form_input('searchphrase', '', $search_attr);
            $submit_attr['id'] = 'search-btn';
            echo form_submit('submit', '<i class=\'fa fa-search\'></i>', $submit_attr);
            echo form_close();
            ?>
        </div>
    </div>
<?php
if ($show_world_clocks == true) {
    include('worldclocks.php');
}
?>
<div id="marquee-cont">
    <div id="ticker-title" onclick="goToLatest()">Latest</div>
    <div id="marquee"><marquee onmouseover="this.stop();" onmouseout="this.start();" id='scroll'>temp</marquee></div>
</div>

<div class="news-upper">
    <div>
        <div><img src="<?= $articles[0]->picture_path ?>" alt="<?= $articles[0]->article_headline ?>" onclick="goToUrl('<?= $articles[0]->article_url ?>')"></div>
        <div>
            <div class="timestamp" style='color:white;'>
                <span class="newswire-highlight">&#9679;</span> 
                <?= date('jS M Y', strtotime($articles[0]->date_and_time)) ?>
            </div>
            <h2 class="top-story"><?= anchor($articles[0]->article_url, $articles[0]->article_headline) ?></h2>
        </div>
    </div>
    <div>
        <?php
        for($i=1; $i<3; $i++) {
            if (isset($articles[$i])) {
            ?>
                <div class="thumbhead">
                    <div>
                        <img src="<?= $articles[$i]->picture_path ?>" alt="<?= $articles[$i]->article_headline ?>" onclick="goToUrl('<?= $articles[$i]->article_url ?>')">
                    </div>
                    <div>
                        <h3><?= anchor($articles[$i]->article_url, $articles[$i]->article_headline) ?></h3>
                    </div>
                </div>
            <?php
            }
        }
        ?>
    </div>
    <div>
        <?php
        if (isset($articles[4])) { ?>
        <h3 class="other-news">Other News</h3>
        <?php
        }
        ?>
        <div class="other-news-grid">
        <?php
        for($i=4; $i<9; $i++) {
            if (isset($articles[$i])) {
            ?>
            <div>
                <div class="timestamp" style='color:white;'>
                    <span class="newswire-highlight">&#9679;</span> 
                    <?= date('jS M Y', strtotime($articles[$i]->date_and_time)) ?>
                </div>
                <div>
                    <?= anchor($articles[$i]->article_url, $articles[$i]->article_headline) ?>
                </div>
            </div>
            <?php
            }
        }
        ?>
        </div>
    </div>
</div><!-- end of news upper -->

<!-- recent posts start -->
<div class="recent-posts">
<?php
for($i=8; $i<11; $i++) {
    if (isset($articles[$i])) {
    ?>
    <div>
        <div>
            <img src="<?= $articles[$i]->picture_path ?>" alt="<?= $articles[$i]->article_headline ?>" onclick="goToUrl('<?= $articles[$i]->article_url ?>')">
        </div>
        <div>
            <h4><?= anchor($articles[$i]->article_url, $articles[$i]->article_headline) ?></h4>
        </div>
    </div>
    <?php
    }
} 
?>
</div>
<!-- recent posts end -->

<div class="strikethrough-headline">
    <div>Other News</div>
</div>

<div class="newswire-text-cards-grid">
<?php
for($i=11; $i<21; $i++) {
    if (isset($articles[$i])) {
    ?>
        <div>
            <div>&#9679;</div>
            <div><?= anchor($articles[$i]->article_url, $articles[$i]->article_headline) ?></div>
        </div>
    <?php
    }
}
?>
</div>

<div class="strikethrough-headline-rhs">
    <div style="opacity: 0;">&nbsp;</div>
</div>

<div class="newswire-alt">
    <?php
    if (isset($articles[21])) { ?>
    <div>
        <div>
            <img src="<?= $articles[21]->picture_path ?>" alt="<?= $articles[21]->article_headline ?>" onclick="goToUrl('<?= $articles[21]->article_url ?>')">
        </div>
        <div>
            <h2 class="top-story"><?= anchor($articles[21]->article_url, $articles[21]->article_headline) ?></h2>
        </div>
    </div>
    <?php
    }
    ?>
    <div class="newswire-g-style">
    <?php
    for($i=22; $i<26; $i++) {
        if (isset($articles[$i])) {
        ?>   
        <div>
            <div class="timestamp" style='color:white'>
                <span class="newswire-highlight">&#9679;</span> 
                <?= date('jS M Y', strtotime($articles[$i]->date_and_time)) ?>
            </div>
                <h4><?= anchor($articles[$i]->article_url, $articles[$i]->article_headline) ?></h4>
        </div>
        <div>
            <img src="<?= $articles[$i]->picture_path ?>" alt="<?= $articles[$i]->article_headline ?>" onclick="goToUrl('<?= $articles[$i]->article_url ?>')">
        </div>
        <?php
        }
    }
    ?>
    </div>
</div>

<style>
    .timestamp {
        color:white;
    }
</style>

<link rel="stylesheet" href="news_module/css/newswire.css">
<script>
<?php
$article_headlines = [];
foreach($articles as $article) {
  $article_headlines[] = addslashes($article->article_headline);
}
?>
const news = <?= $headlines_json ?>;
const ticker_img = '<span class="ticker-img" style="font-size: 1.1em; margin: 0  7px;">&#10022;</span>';
let tickerText = ticker_img;
for(let i=0; i<news.length; i++){
  tickerText+=news[i];
  //adds the ticker_img in between news items
  if(i!=news.length-1){
    tickerText+=ticker_img;
  }
}

document.querySelector("#scroll").innerHTML = tickerText;

function goToLatest() {
    var latestNewsUrl = '<?= $articles[0]->article_url ?>';
    goToUrl(latestNewsUrl);
}

function goToUrl(targetUrl) {
  window.location.href = targetUrl;
}

function adjustPage() {
  var searchphrase = document.getElementById("searchphrase");
  var searchBtn = document.getElementById("search-btn");
  searchBtn.style.height = searchphrase.offsetHeight + 'px';
  var topStoryDiv = document.querySelector(".news-upper > div:nth-child(1)");
  var otherNewsDiv =  document.querySelector(".news-upper > div:nth-child(3)");
  otherNewsDiv.style.height = topStoryDiv.offsetHeight + 'px';
}

window.addEventListener('resize', adjustPage)
</script>