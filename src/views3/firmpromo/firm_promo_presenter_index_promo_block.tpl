<?/*$i=0;foreach ($items as $item) {$i++;?>

    <style>
        .index--flex-cards{
            display: flex;
        }
        .index--flex-cards>img{
            max-width: 100px;
        }
    </style>
    <div class="index--flex-cards">
        <?if($item['image']){?>
            <img class="img-fluid" src="<?=$item['image']?>" alt="<?=encode($item['name'])?>" />
        <?} else {?>
            <img class="img-fluid" src="/css/img/no_img.png" alt="Нет фотографии" />
        <?}?>
            <div class="index--flex-cards-list">
                <div class="article__info article__info_special"><?= $item['flag_is_infinite'] ? 'Постоянная акция' : 'с ' . $item['time_beginning_short'] . ' по ' . $item['time_ending_short']?></div>
                <a class="article__heading article-card__heading article__heading_special" href="<?=$item['link']?>"><?=$item['name']?></a>
            </div>
    </div>

<?/*
    <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone">
        <article class="article article-card">
            <div class="article__img">
                <?if($item['image']){?>
                    <img class="img-fluid" src="<?=$item['image']?>" alt="<?=encode($item['name'])?>" />
                <?} else {?>
                    <img class="img-fluid" src="/css/img/no_img.png" alt="Нет фотографии" />
                <?}?>
                <?if (isset($item['rubric']) && $item['rubric']) {?>
                    <span class="article__tag"><?=$item['rubric']?></span>
                <?}?>
            </div>
            <div class="article__info article__info_special"><?= $item['flag_is_infinite'] ? 'Постоянная акция' : 'с ' . $item['time_beginning_short'] . ' по ' . $item['time_ending_short']?></div>
            <a class="article__heading article-card__heading article__heading_special" href="<?=$item['link']?>"><?=$item['name']?></a>
        </article>
    </div>
    */?>
