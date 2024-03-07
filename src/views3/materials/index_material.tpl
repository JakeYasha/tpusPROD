<? if ($item) {?>
    <div class="article-page__title-block">
        <a class="tp-mt-btn tmb-back" href="/materials/">Назад</a>
        <a class="tp-mt-btn tmb-red" href="/materials/<?=$rubric_hrefname;?>"><?= $item['rubric'] ?></a>
        <h1><?= $item['name'] ?></h1>
        <?
        if (APP_IS_DEV_MODE){
            ?>
        <a href="https://www.tovaryplus.ru/firm-manager/material/<?=$item['id'];?>/" target="_blank">РЕДАКТИРОВАТЬ МАТЕРИАЛ</a>    
            <?
        }
        ?>
        <div class="article-page__actions"><span class="article__info article__info_article-page"><?= $item['timestamp_last_updating'] ?> <?
        if (APP_IS_DEV_MODE){
            if ($item['stat_see']>=5){
                echo ' | '.$item['stat_see'];
            }
        }
        
        ?>
        <span class="article__info">
        <?    
            if (true){?>
                <span>
                    <img src="/img3/eye.png"/>
                <?
                    echo ' '.$item['stat_see'].' ';
                ?>
                </span>
                <?
                }
        ?></span>    
    </span><script src="https://yastatic.net/share2/share.js"></script>
            <div class="ya-share2" data-curtain data-shape="round"  data-title="На Онлайн-газете ТОВАРЫ+, интересная статья: <?= $item['name'] ?>" data-services="messenger,vkontakte,facebook,odnoklassniki,telegram,twitter,viber,whatsapp,moimir"></div>
        </div>
    </div>
<?

if ($material_image->iconLink())
{
?>
    <div class="article-page__img">
        <img class="img-fluid" src="<?=$material_image->iconLink();?>">
    </div>
<?
}
?>
    <div class="article-page__content tp-mt-global-content">
        <div class="tp-mt-head-title"><?= $item['short_text'] ?></div>
        <?= $item['text'] ?>
        <hr>
        <?=$tags;?>
        <div class="tp-mt-text-share">
            <div class="ya-share2" data-curtain data-shape="round"  data-title="На Онлайн-газете ТОВАРЫ+, интересная статья: <?= $item['name'] ?>" data-services="messenger,vkontakte,facebook,odnoklassniki,telegram,twitter,viber,whatsapp,moimir"></div>
        </div>
        
        <div class="tp-my-4">
            <a class="tp-mt-btn tmb-back" href="/materials/">Назад</a>
            <a class="tp-mt-btn tmb-red" href="/materials/<?=$rubric_hrefname;?>"><?= $item['rubric'] ?></a>
        </div>
        
        <div class="read-more module">
            <p class="read-more__heading">Читайте также</p>
            <ul class="list read-more__list">
                <? foreach($last_materials as $last_material) { ?>
                    <li class="read-more__list--item"><a class="read-more__list--link" href="<?=$last_material['link']?>"><?=$last_material['name']?></a></li>
                <? } ?>
            </ul>
        </div>
        
    </div>



<?if ((isset($item['advert_restrictions'])) && ($item['advert_restrictions']!="Не определен")){
    ?>
    <div class="bottom_alert" style="display: block;font-size: 28px;font-style: italic;font-weight: 100;color: #FFF;">
        <p style="max-width: 1040px;text-align: center;margin-left: auto;margin-right: auto;"><?=$item['advert_restrictions']?></p>
    </div>
    <?
}?>


<? } ?>