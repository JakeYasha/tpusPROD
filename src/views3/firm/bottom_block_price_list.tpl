<div class="item_info  offset-none">
    <div class="search-result">
        <?= $tabs?>
        <?= $inside_bread_crumbs?>
        <? if ($filters['id_catalog'] !== null || $filters['q'] !== null) {?>
            <div class="filter-list__sortbox_no_wrap">
                <form class="js-autocomplete-form form price-form-search" action="<?= $url?>" method="get">
                    <button class="btn btn_search" type="submit"><img alt="Поиск" src="/img3/search.png"></button>
                    <?= $autocomplete?>
                    <input type="hidden" name="mode" value="price" />
                </form>
                <?= app()->chunk()->set('filters', $filters)->set('link', $url)->render('common.display_mode')?>
                <?= $sorting?>
            </div>
            <div class="firm-bottom-block" style="margin-top: 20px;">
                <h2><?= app()->metadata()->getHeader()?></h2>
                <? if ($total_founded !== 0) {?><div class="description mbp20"><p>Найдено подходящих предложений: <?= $total_founded?> из <?= $total_price_list_count?></p></div><? }?>
            </div>	
        <? } else {?>
            <div class="block-title">
                <div class="block-title__decore"></div>
                <h2 class="block-title__heading block-title__heading_h1"><?= app()->metadata()->getHeader()?></h2>
            </div>
            <form class="js-autocomplete-form  form price-form-search" action="<?= $url?>" method="get">
                <?= $autocomplete?>
                <button class="btn btn_search" type="submit"><img alt="Поиск" src="/img3/search.png"></button>
                <input type="hidden" name="mode" value="price" />
            </form>
            <p>Всего предложений: <?= $total_price_list_count?></p>
        <? }?>

        <? if ($total_founded === 0 && ($filters['id_catalog'] !== null || $filters['q'] !== null)) {?>
            <div class="module stat-info">
                <p>К сожалению, информации по запросу<?= $filters['q'] !== null ? ' '.encode('"'.$filters['q'].'"') : ''?> в прайс-листе не найдено.</p>
            </div>
        <? }?>

        <? if ($filters['id_catalog'] === null && $filters['q'] === null) {?>
            <?= $items?>
            <div class="black-block">Рубрики прайс-листа фирмы</div>
            <?= $tags?>
            <? if ($item->hasFiles()) {?>
                    <div class="firm-bottom-block">
                        <h2>Ссылки для просмотра и скачивания файлов</h2>
                        <div class="uploaded-files">
                            <ul>
                                <? foreach ($files as $img) {?><li>
                                        <a class="img" href="<?= $img->link()?>" rel="nofollow"><img src="<?= $img->thumb('_s')?>" /></a>
                                        <a class="name" href="<?= $img->link()?>" rel="nofollow"><?= $img->name()?></a>
                                        <span><?= $img->val('file_extension')?>, <?= $img->getFormatSize("", 0)?></span>
                                        <a href="#" class="js-action js-remove-firm-file img-del-btn" data-id="<?= $img->id()?>" rel="nofollow"></a>
                                    </li><? }?>
                            </ul>
                        </div>
                    </div>
            <? }?>
        <? } elseif ($filters['id_catalog'] !== null) {?>
            <?= $tags?>
            <?= $items?>
        <? } else {?>
            <?= $items?>
        <? }?>
        <?= $pagination?>
    </div>
    <br/>
    <div class="pre_footer_adv_block">
        <?= app()->chunk()->render('adv.bottom_banners')?>
    </div>
</div>