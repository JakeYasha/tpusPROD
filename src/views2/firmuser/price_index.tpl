<?= $bread_crumbs ?><a title="Выгрузить в xls" target="_blank" class="download-btn" href="<?= app()->linkFilter('/firm-user/price/', $filters, ['export' => 'xls']) ?>"></a>
<div class="black-block">Прайс-лист фирмы</div>
<div class="item_info">
    <div class="search_result in-firm-manager">		
        <a href="/firm-user/price/add/" class="default-red-btn" style="margin-bottom: 10px;" title="Товары и услуги">Добавить позицию</a>
        <a href="/firm-user/price/upload/" class="default-red-btn" style="margin-bottom: 10px;">Загрузить списком</a>
        <? if ($total_founded !== 0) { ?>
            <div class="description mbp20">
                <? if ($catalog instanceof \App\Model\PriceCatalog && $catalog->exists()) { ?><h1 class="price-header"><?= $catalog->name() ?><? } ?></h1>
                <p>Найдено подходящих предложений: <?= $total_founded ?> из <?= $total_price_list_count ?></p>
            </div><? } ?>
        <? if ($filters['id_catalog'] !== null || $filters['q'] !== null) { ?>
            <div class="search_price_field">
                <form class="js-autocomplete-form" action="<?= $url ?>" method="get"><?= $autocomplete ?><input type="submit" value="" class="submit"/><input type="hidden" name="mode" value="price" /></form><?= $sorting ?>
            </div>
        <? } else { ?>
            <div class="search_price_field">
                <form class="js-autocomplete-form" action="<?= $url ?>" method="get"><?= $autocomplete ?><input type="submit" value="" class="submit"/><input type="hidden" name="mode" value="price" /></form><?= $sorting ?>
            </div>
            <div class="description" style="margin-top: 10px;">
                <p>Всего предложений: <?= $total_price_list_count ?></p>
            </div>
        <? } ?>
        <br/>
        <? if ($total_founded === 0 && ($filters['id_catalog'] !== null || $filters['q'] !== null)) { ?>
            <div class="cat_description">
                <p>К сожалению, информации по запросу<?= $filters['q'] !== null ? ' ' . encode('"' . $filters['q'] . '"') : '' ?> в прайс-листе не найдено.</p>
            </div>
        <? } ?>
        <? if ($filters['id_catalog'] === null && $filters['q'] === null) { ?>
            <?= $items ?>
        <? } elseif ($filters['id_catalog'] !== null) { ?>
            <div class="clearfix">
                <?= $tags ?>
                <div class="search_result"><?= $items ?></div>
            </div>
        <? } else { ?>
            <div class="clearfix">
                <div class="search_result"><?= $items ?></div>
            </div>
        <? } ?>
    </div>

    <div class="search_result">
        <?=$pagination ?? ''?>
    </div>
    <? if ($filters['id_catalog'] === null) { ?>
        <div class="black-block">Рубрики прайс-листа фирмы</div>
        <?= $tags ?>
    <? } ?>
    <div class="search_result">
        <div class="delimiter-block"></div>
        <div class="notice-dark-grey">
            <p>Есть вопросы? Позвони в техподдержку сайта: <span style="font-size: 21px;">+7&nbsp;(4852)&nbsp;42-97-82</span> или <a class="fancybox fancybox.ajax" href="/firm-feedback/get-feedback-form/38352/10/?id_option=18&old=1" rel="nofollow"><button style="width: 200px;"> Оставь сообщение </button></a></p>
        </div>
    </div>
</div>