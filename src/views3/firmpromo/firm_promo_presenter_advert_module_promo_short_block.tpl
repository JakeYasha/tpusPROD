<? if ($items) { ?>
    <? $i = 0;$visible = true;foreach ($items as $item) { $i++;?>
        <div class="mdc-layout-grid__cell--span-3-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone">
            <article class="article article-card">
                <div class="article__img">
                    <a href="<?=$item['link']?>">
                        <? if ($item['image']) { ?><img class="img-fluid" src="<?= $item['image'] ?>" alt="<?= encode($item['name']) ?>" /><? } else { ?><img class="img-fluid" src="/css/img/no_img.png" alt="Нет фотографии" /><? } ?>
                    </a>
                </div>
                <div class="article__info article__info_special">
                    <?=$item['flag_is_infinite'] ? '<span class="promo-infinite">Постоянная акция</span>' : 'с '.$item['time_beginning'].' по '.$item['time_ending']?>
                </div>
                <a class="article__heading article-card__heading article__heading_special" href="<?= $item['link'] ?>"><?= $item['name'] ?></a>
            <!-- sssssss  -->
            </article>
        </div>
    <? } ?>
<? } ?>