<div class="newswire">
    <div class="newswire-article-grid">
        <div>
            <h1><?= $article_headline ?></h1>
            <div class="timestamp"><span class="newswire-highlight">&#9679;</span> 
                Published <?= date('l jS F Y', strtotime($date_and_time)) ?></div>
                <?php
                if ($picture !== '') {
                ?><div><img src="<?= $picture_path ?>" alt="<?= $article_headline ?>"></div><?php
                }
                ?>
            <div class="article-body">
                <?= nl2br($article_body) ?>
            </div>
        </div>
        <div>
            <h3 class="other-news">Other News</h3>
            <div class="other-news-grid">
                <?php
                foreach($articles as $article) {
                ?>
                <div>
                    <div class="timestamp">
                        <span class="newswire-highlight">&#9679;</span> 
                        <?= date('jS M Y', strtotime($article->date_and_time)) ?>
                    </div>
                    <div><?= anchor($article->article_url, $article->article_headline) ?></div>
                </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="news_module/css/newswire.css"> 
<script>
var youtubeVideos = document.getElementsByClassName("youtube-video");

function buildYouTubeVids() {

    for(i=0; i<youtubeVideos.length; i++) {

        var videoId = youtubeVideos[i].innerHTML;

        while (youtubeVideos[i].firstChild) {
            youtubeVideos[i].removeChild(youtubeVideos[i].lastChild);
        }

        var parentDiv = document.querySelector("body > div.wrapper > main > div > div > div:nth-child(1)");

        var magicNumber = 312/560;
        var targetVideoWidth = parentDiv.offsetWidth;
        var targetVideoHeight = targetVideoWidth * magicNumber;

        var youtubeIFrame = document.createElement("iframe");
        youtubeIFrame.setAttribute("class", "youtube-i-frame")
        youtubeIFrame.setAttribute("width", targetVideoWidth);
        youtubeIFrame.setAttribute("height", targetVideoHeight);
        youtubeIFrame.setAttribute("src", "https://www.youtube.com/embed/" + videoId)
        youtubeIFrame.setAttribute("frameborder", "0");
        youtubeIFrame.setAttribute("allow", "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture");
        youtubeIFrame.setAttribute("allowfullscreen", true);

        youtubeVideos[i].style.display = 'block';
        youtubeVideos[i].appendChild(youtubeIFrame);
    }

}

setTimeout(() => {
    buildYouTubeVids();
}, 500);

function adjustPage() {
    //sort out the youtube videos
    var youtubeVideos = document.getElementsByClassName("youtube-i-frame");

    for(i=0; i<youtubeVideos.length; i++) {
        youtubeVideos[i].setAttribute("width", 1);
        youtubeVideos[i].setAttribute("height", targetVideoHeight);        
    } 

    var parentDiv = document.querySelector("body > div.wrapper > main > div > div > div:nth-child(1)");
    var magicNumber = 312/560;
    var targetVideoWidth = Math.floor(parentDiv.offsetWidth);
    var targetVideoHeight = Math.floor(targetVideoWidth * magicNumber);

    for(i=0; i<youtubeVideos.length; i++) {
        youtubeVideos[i].setAttribute("width", targetVideoWidth);
        youtubeVideos[i].setAttribute("height", targetVideoHeight);        
    }  
}
</script>


<style>
.newswire-article-grid {
   display: grid;
   grid-gap: 1em;
   padding-bottom: 5em;
}

.newswire-article-grid > div:nth-child(2) {
    border-top: 3px var(--newswire-primary-bg) solid;
}

.newswire-article-grid h1 {
    font-size: 45px;
}

.newswire-article-grid img,
.article-body {
    margin-top: 2em;
}

@media (min-width: 1px) {

    .newswire-article-grid {
       grid-template-columns: 1fr;
    }

}

@media (min-width: 1000px) {

    .newswire-article-grid {
       grid-template-columns: 7fr 2fr;
    }

}
</style>

<script>
window.addEventListener('resize', adjustPage);
</script>