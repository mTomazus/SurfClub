<?php
foreach($articles as $article) {
    echo '<h4>'.$article->page_title.'</h4>';
    echo '<p>'.anchor($article->article_url, $article->article_url).'</p>';
    echo '<p>'.date('l jS \o\f F Y', $article->date_created).'</p>';
    echo '<hr>';
}