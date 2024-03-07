<? /* if($items && app()->location()->currentId() != '76004'){ */?>

    
<? if ($items) {?>
    <? foreach ($items as $k => $advert_module) {?>
        <div class="mdc-layout-grid__cell">
            <article class="article article-card">
                <div class="article__img">
                    <? if (isset($advert_module['image']) && isset($advert_module['full_image'])) {?>
                        <a href="/advert-module/url/<?=$advert_module['id']?>/">
                            <img class="img-fluid" src="<?= $advert_module['image']?>" alt="">
                        </a>
                    <? } else if (isset($advert_module['image'])) {?>
                        <a href="/advert-module/url/<?=$advert_module['id']?>/">
                            <img class="img-fluid" src="<?= $advert_module['image']?>" alt="">
                        </a>
                    <? } else { ?>
                    <? } ?>
                </div>
                <div class="article__info article__info_special">c <?= CDateTime::gmt("d.m.Y", $advert_module['time_beginning_short'])?> по <?= CDateTime::gmt("d.m.Y", $advert_module['time_ending_short'])?></div><!--123-->
                <a class="article__heading article-card__heading article__heading_special" href="/advert-module/url/<?=$advert_module['id']?>/"><?= $advert_module['header']?></a>
                <!--p><?= $advert_module['text']?><br/><span style="font-size: 11px; color: #A96060;"><?= $advert_module['about_string']?></span></p-->
            </article>
        </div>
    <? } ?>
    <!--/noindex-->
<? } ?>