<div class="mdc-layout-grid">
    <?= $bread_crumbs?>
    <?
    if (isset($position['top'])){
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
    ?>
    <div class="mdc-layout-grid__inner" style="margin-top: 2rem">
        <div class="mdc-layout-grid__cell--span-2-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
            <? if ($item['image']) {?>
                <img class="img-fluid" src="<?=$item['image']?>" alt="<?=encode($item['name'])?>" />
            <? } else if($item['images']) {?>
                <div class="price-images">
                    <ul>
                        <? foreach ($item['images'] as $img){?>
                            <li style="width:50px;height: 50px;display: inline-block;">
                                <a style="width:100%;height:100%;" class="fancybox" href="<?=$item['images_base_path']?>/<?=$img->val('file_subdir_name').'/'.$img->val('file_name').'.'.$img->val('file_extension')?>" rel="price_images">
                                    <img src="<?=$item['images_base_path']?>/<?=$img->val('file_subdir_name').'/'.$img->val('file_name').'.'.$img->val('file_extension')?>" alt="<?=$item['name']?>" style="width:100%;" />
                                </a>
                            </li>
                        <?}?>
                    </ul>
                </div>
            <?} else {?>
                <img class="img-fluid no-image" src="/css/img/firm_logo.png" alt="<?=encode($item['name'])?>" />
            <?}?>
        </div>
        <div class="mdc-layout-grid__cell--span-10-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
            <h1 class="brand-list__item--heading product-title"><?= $item['name']?></h1>
            <?/*todo<div class="to_fav"><a href="#">Добавить в избранное</a></div>*/?>
            <? if ($item['price'] !== null) {?>
                <?if($item['price']) {?>
                    <p class="product-price"><?=$item['price']?> <?= $item['currency']?> <?if($item['unit']){?><span>цена за <?= $item['unit']?></span><?}?></p>
                    <?if($item['price_wholesale'] && $item['price_retail']){?>
                    <p class="product-price<?=($item['price_wholesale']) ? ' under_price' : ''?>"><?=$item['price_wholesale']?> <?= $item['currency']?><?=($item['price_wholesale']) ? '&nbsp; оптом' : ''?> <?if($item['unit']){?><span>цена за <?= $item['unit']?></span><?}?></p>
                    <?}?>
                <?}?>
                <? if ($item['old_price']) {?>
                    <p class="product-price"><?=$item['old_price']?> <?= $item['currency']?> <?if($item['unit']){?><span>цена за <?= $item['unit']?></span><?}?></p>
                <? }?>

                <?=app()->chunk()->set('firm', $firm)->set('item', $item)->set('id', $item['id'])->render('common.button_set_price_big')?>
            <? } else {?>
                <?=app()->chunk()->set('firm', $firm)->set('item', $item)->set('id', $item['id'])->set('style', 'float: left; width: 100%')->render('common.button_set_price_big')?>
            <? }?>
        </div>
    </div>
    <div class="divider"></div>
    <? if (str()->length($item['info']) > 2) {?><p class="product-info"><?= $item['info']?></p><? }?>
    <? if (str()->length($item['production']) > 2) {?><p class="product-info">Производство: <strong><?=$item['production']?></strong><?if($item['pack']){?>, фасовка: <strong><?=$item['pack']?></strong><?}?></p><?} else {?><?if($item['pack']){?><p>Фасовка: <strong><?=$item['pack']?></strong></p><?}?><?}?>
    <? if (str()->length($item['vendor']) > 2) {?><p class="product-info">Производитель: <strong><?=$item['vendor']?></strong></p><?}?>
    <? if (str()->length($item['info']) < 2 && str()->length($item['production']) < 2){?><p class="product-info">&nbsp;</p><?}?>
    <div class="brand-list">
        <div class="brand-list__item">
            <div class="mdc-layout-grid__inner">
                <div class="mdc-layout-grid__cell--span-9-desktop mdc-layout-grid__cell--span-5-tablet mdc-layout-grid__cell--span-4-phone">
                    <h2 class="brand-list__item--heading">
                        <a class="name_firm" href="<?= $firm->link()?>"><?= $firm->name()?></a>
                    </h2>
                    <p class="brand-list__item--text"><?= $firm->activity()?></p>
                    <?if ($firm_catalog_analog_prices_count > 1) {?>
                        <p class="brand-list__item--text"><a rel="nofollow" href="<?=$firm_catalog_analog_prices_url?>">Еще <?=$firm_catalog_analog_prices_count?> похожих предложений фирмы в рубрике "<?=$firm_catalog_name_analog_prices?>"</a></p>
                    <?}?>
                    <p class="brand-list__item--text">
                        <a rel="nofollow" href="<?=$firm->link().'?mode=price'?>">Все товары и услуги фирмы [<?=$firm_all_prices_count?>]</a>
                    </p>
                    <? if ($firm->hasPhone()) {?>
                        <div class="brand-list__item--service">
                            <div class="brand-list__item--info">Телефон:</div>
                            <div class="brand-list__item--content"><?= $firm->renderPhoneLinks() ?></div>
                        </div>
                    <? }?>
                    <div class="brand-list__item--service">
                        <div class="brand-list__item--info">Адрес:</div>
                        <div class="brand-list__item--content"><?= $firm->address() ?></div>
                    </div>
                    <? if ($firm->hasWeb()) { ?>
                        <div class="brand-list__item--service">
                            <div class="brand-list__item--info">Сайт:</div>
                            <div class="brand-list__item--content"><?= $firm->renderWebLinks() ?></div>
                        </div>
                    <? } ?>
                </div>
            </div>
        </div>
    </div>

    <p class="product-info">За более полной информацией о <?=($item['id_group'] == 44 ? 'услуге' : 'товаре')?> <?=$item['name']?>, по вопросам заказа<?=($item['id_group'] == 44 ? ' услуги' : ', покупки и доставки товара')?>, пожалуйста, обращайтесь в фирму <?=$firm->name()?>. Актуальные цены <?=($item['id_group'] == 44 ? 'на услугу' : 'и наличие товара')?> на текущий момент вы можете узнать по телефону <?=app()->location()->currentName('prepositional')?>: <?=$firm->phone()?></p>
    <p class="product-info">&nbsp;</p>
    <p class="product-info">Ответственность за достоверность и актуальность информации по предложению, несет фирма предоставившая данную информацию в своем прайс-листе для размещения на сайте.</p>
    <p class="product-info">&nbsp;</p>
    <?if(strlen($firm_catalog_name_analog_prices) > 2) {?>
        <a href="<?=$current_catalog_link?>" class="btn btn_primary btn_full-width">Посмотрите обязательно другие фирмы предлагающие "<?=$firm_catalog_name_analog_prices?>" >>></a>
    <?}?>

    <?= $price_on_map?>
    <?if($other_items||$additional_items){?>
        <div class="black-block">Возможно вас также заинтересуют:</div>
    <?}?>
    <div class="search-result">
        <?= $other_items?>
        <?= $additional_items?>
    </div>

    <div class="search-result">
        <? if ($item['id_group'] != 44) {?>
            <div class="attention-info">
                <div>Описание и изображение товаров на сайте носят информационный характер и могут отличаться от фактического описания, технической документации от производителя и реального вида товаров. Рекомендуем уточнять наличие желаемых функций и характеристик товаров у продавца.</div>
            </div>
        <? }?>
        <?=app()->chunk()->render('adv.top_banners')?>
        <?=app()->chunk()->render('adv.middle_banners')?>
        <? if (count($price_parent_catalogs) > 0) {?>
            <div class="module stat-info">
                <p>Если Вы не нашли на странице то, что искали или хотите найти дополнительную информацию по вашему запросу, попробуйте воспользоваться формой поиска или пройдите по ссылкам на следующие разделы:</p>
                <ul>
                    <?foreach ($price_parent_catalogs as $price_parent_catalog) {?>
                        <li><a href="<?=$price_parent_catalog['link']?>"><?=$price_parent_catalog['name']?></a></li>
                    <?}?>
                </ul>
                <br/>
            </div>
        <?}?>
    </div>
    <br/>
    <div class="pre_footer_adv_block">
        <?=app()->chunk()->render('adv.bottom_banners')?>
    </div>
    
    <?
    if (isset($position['bottom'])){
        echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
    }
    ?>
    
</div>
<?=$advert_restrictions?>

