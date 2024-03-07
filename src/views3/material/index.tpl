<? if ($item) {?>
    <div class="article-page__title-block">
        
        <a class="article-page__tag" href="/materials/">Назад -</a>
        <div class="article-page__tag"><?= $item['rubric'] ?></div>
        <h1><?= $item['name'] ?></h1>
        <?
        if (APP_IS_DEV_MODE){
            ?>
        <a href="https://www.tovaryplus.ru/firm-manager/material/<?=$item->id();?>/" target="_blank">РЕДАКТИРОВАТЬ МАТЕРИАЛ</a>    
            <?
        }
        ?>
        <div class="article-page__actions"><span class="article__info article__info_article-page"><?= $item['timestamp_last_published'] ?></span><a class="share" href="">Поделиться<img src="/img/share.png"></a></div>
    </div>
<?
//echo '<pre>';
//var_dump($item);
//echo '</pre>';
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
             <script src="https://yastatic.net/share2/share.js"></script>
            <div class="ya-share2" data-curtain data-shape="round" data-services="messenger,vkontakte,facebook,odnoklassniki,telegram,twitter,viber,whatsapp,moimir"></div>
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
    <div class="bottom_alert" style="display: block;font-size: 32px;font-style: italic;font-weight: 100;">
        <?=$item['advert_restrictions']?>
    </div>
    <?
    if (APP_IS_DEV_MODE){
       ?>
           <script>
           alert('123');
           </script>
           <?
    }
}else{
    if (APP_IS_DEV_MODE){
        var_dump($item);
        ?>
           <script>
           alert('222');
           </script>
           <?
    }
}?>
    

<? } ?>