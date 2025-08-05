<div id="slide-nav" class="slide justify-content-around" mx-get="competitions" mx-select=".side-nav" mx-trigger="load" mx-target="#result">
    <div id="result"></div>
    <div class="slide-header">
        <div class="slide-footer">
            <?= anchor('competitions', '<i class="fa fa-user"></i>') ?>
            <div class="slide_user">
                <span><?= $user->name ?></span>
                <h3><?= $user->role ?></h3>
            </div>
            <?= anchor('competitions/logout', '<i class="fa fa-sign-out"></i>') ?>
        </div>
    </div>
</div>