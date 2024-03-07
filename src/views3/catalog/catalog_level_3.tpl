<div class="mdc-layout-grid">
    <?
//    if (APP_IS_DEV_MODE){
//        echo '<a href="'.$href_idcat.'" target="_blank">Редактировать в CMS</a>';
//        //if ($promo_catalog_data != null) {
//            //var_dump($idcat);
//        //}
//            
//        //echo '</h1>';
//    }
    if ((new \App\Model\Administrator())->userComponent()->getFromSession()->exists()) {
			echo '<a href="'.$href_idcat.'" target="_blank">Редактировать в CMS</a>';
    }
    if (isset($position['top'])){
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
    ?>
<?= $bread_crumbs?>
<?if($items_exists){?>
    <div class="block-title">
        <div class="block-title__decore"></div>
        <h1 class="block-title__heading block-title__heading_h1"><?=isset($ext_title) && $ext_title ? $ext_title : $title?></h1>
    </div>
        <?if($top_text){?>
            <p><?=$top_text?></p>
        <?} else {?>
            <p>Обзор <?=$filters['mode'] === 'price' ? 'предложений' : 'компаний'?> <?=app()->location()->currentName('genitive')?>. <?=$annotation_text?></p>
        <?}?>

        <?if($advert_age_restrictions){?><p>Категория: <?=$advert_age_restrictions?></p><?}?>
    <?/*mobile_filter_here*/?>
    <?= app()->chunk()->set('filter', $filters)->set('items', $tags)->set('id_group', $group->id())->set('mode', 'top')->render('common.catalog_tags')?>

    <div class="brands module">
    <?if (app()->sidebar()->getParam('top_brands')) {?>
        <div class="block-title brand-list__actions_mobile">
            <div class="block-title__decore"></div>
            <h2 class="block-title__heading">Бренды</h2>
        </div>
        <div class="filter-list brand-list__actions_mobile">
            <?
                $top_brands = app()->sidebar()->getParam('top_brands');
                $brands_active = app()->sidebar()->getParam('brands_active');
                $url = app()->url();
            ?>
            <?$unique_brand_names = [];
            foreach($top_brands as $brand) {
                if (in_array(str()->firstCharToUpper(str()->toLower($brand['site_name'])), $unique_brand_names)) {
                    continue;
                } else {
                    $unique_brand_names []= str()->firstCharToUpper(str()->toLower($brand['site_name']));
                }?>
                <label class="filter-list__item">
                    <input class="filter-list__radio" <?=(in_array($brand['id'], $brands_active) ? 'checked="checked"' : '')?> type="radio" name="brand">
                    <div class="filter-list__check"><a style="text-decoration: none;color: #34404e;" href="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['brand' => $brand['id']]))?>"><?=str()->firstCharToUpper(str()->toLower($brand['site_name']))?></a></div>
                </label>
            <?}?>
        </div>
    <?}?>

    <? if ($promo_catalog_data != null) {
        $promo_catalog_id = $promo_catalog_data['price_catalog_id'];
        $promo_catalog_title = $promo_catalog_data['price_catalog_name']; ?>
        <br/>
        <div class="brand-list__alert">
            <div class="mdc-layout-grid__inner">
                <div class="mdc-layout-grid__cell--span-12">
                    <a style="color: #fff;text-decoration:none;" class="rubric_promo_button" rel="nofollow" href="<?=app()->link(app()->linkFilter('/advert-module/', ['id_group' => $group->val('id_group'), 'id_subgroup' => $sub_group->val('id_subgroup')]))?>">Акции и скидки в рубрике "<?=$promo_catalog_title?>"</a>
                </div>
            </div>
        </div>
        <br/>
    <? } ?>

    <?=app()->chunk()->render('adv.catalog_top_banners')?>

    <?=$tabs?>
    <?=$items?>
    <?= $pagination?>

        
    <div>
        <!-- Yandex.Market Widget -->
        <script async src="https://aflt.market.yandex.ru/widget/script/api" type="text/javascript"></script>
        <script type="text/javascript">
            (function (w) {
                function start() {
                    w.removeEventListener("YaMarketAffiliateLoad", start);
                    w.YaMarketAffiliate.createWidget({type:"models",
            containerId:"marketWidget",
            params:{clid:2564601,
                themeShowTitle:false,
                searchText:"огородные инструменты",
                searchType:"also_viewed",
                themeId:1 } });
                }
                w.YaMarketAffiliate
                    ? start()
                    : w.addEventListener("YaMarketAffiliateLoad", start);
            })(window);
        </script>
        <!-- End Yandex.Market Widget -->

        <div id="marketWidget"></div>
    </div>

    <div class="module stat-info_v2">
        <?if (APP_IS_DEV_MODE) {?><h3><a rel="nofollow" href="/statistics/dynamic/?id_catalog=<?=$item->id()?>" class="attention-info">Статистика раздела</a></h3><?}?>
        <p class="brand-list__item--text">В рубрике  &QUOT;<?=$title?>&QUOT; <?=app()->location()->currentName('prepositional')?> найдено <?=$total_prices_count?> <?=\CWord::ending($total_prices_count, ['предложение','предложения','предложений'])?> в <?=$total_firms_count?> <?=\CWord::ending($total_firms_count, ['фирме','фирмах','фирмах'])?>.</p>
        <p class="brand-list__item--text"><?=$annotation_text?></p>
    </div>
    <div class="search-result-bottom">
        <?/*<a class="btn-base btn-red" href="<?=$filters['mode'] === 'price' ? app()->link(app()->linkFilter($item->link(), array_merge($filters, ['page' => null]), ['mode' => false])) : app()->link(app()->linkFilter($item->link(), array_merge($filters, ['page' => null]), ['mode' => 'price']))?>">Посмотреть все <?=$filters['mode'] === 'price' ? 'фирмы' : 'предложения'?></a>*/?>
        <?if($bottom_text){?><div class="bot_text"><?=$bottom_text?></div><?}?>
    </div>
    <?/*<button class="show_more_results js-show-more">Показать еще результаты</button>*/?>
    <?=$promo_items?>
    <?if($analog_catalogs){?>
    <div class="black-block">Предложения товаров и услуг в других категориях по запросу "<?=$item->val('name')?>"</div>
    <?= app()->chunk()->set('items', $analog_catalogs)->set('id_group', $group->id())->set('mode', 'bottom')->render('common.catalog_tags')?>
    <?}?>
    <br/>
    <div class="pre_footer_adv_block">
    <?=app()->chunk()->render('adv.bottom_banners')?>
    </div>
    <?if($sub_group->exists() && $sub_group->val('text2') && !$item->id()){?><div class="cat_description"><?=app()->metadata()->replaceLocationTemplates($sub_group->val('text2'))?></div><?}?>
    <?=$advert_restrictions?>
    <?if (APP_IS_DEV_MODE) {
        if($_SERVER['REQUEST_URI'] == '/catalog/44/278/53493/uslugi-zvonkovyh-centrov-call-centra.htm') {?>
            <style>
                .notification {
                    display: block;overflow: hidden;z-index: 10000;position: fixed;bottom: 0;right: 0;background-color: #000;width: calc(100% - 200px);opacity: 0.7;-moz-opacity: 0.4;filter: progid:DXImageTransform.Microsoft.Alpha(opacity=40);-khtml-opacity: 0.5;text-align: center;
                }
                .close-notification {
                    height: 24px;width: 24px;display: block;background-color: #cd203c;position: absolute;top: 4px;right: 4px;content: "+";color: #fff;font-weight: normal;font-size: 12px;-webkit-transform: rotate(45deg);-moz-transform: rotate(45deg);-ms-transform: rotate(45deg);-o-transform: rotate(45deg);transform: rotate(45deg);vertical-align: middle;font-size: 43px;border-radius: 18px;line-height: 24px;cursor: pointer;box-shadow: 0 0 2px #cd203c;transition: 0.5s ease;
                }
                .close-notification:hover {
                    -webkit-transform: rotate(-45deg);-moz-transform: rotate(-45deg);-ms-transform: rotate(-45deg);-o-transform: rotate(-45deg);transform: rotate(-45deg);transition: 0.5s ease; color: #000;
                }
                .text-notification {
                    display:inline-block;font-size:26px;color:#fff;line-height: 34px;width: 60%;vertical-align: middle;margin: 16px 0;
                }
                .small-text-notification {
                    color: #fff;font-size: 15px;margin-top: 28px;padding: 0 10px;margin:0;line-height: 16px;
                }
                .notification.hidden {
                    animation: dropdown 1s forwards;
                    -webkit-transform: rotate(-45deg);-moz-transform: rotate(-45deg);-ms-transform: rotate(-45deg);-o-transform: rotate(-45deg);transform: rotate(-45deg);transition: 0.5s ease;
                }
                .notification-buttons {
                    margin: 20px 0;display: inline-block;vertical-align: middle;
                }
                .notification-buttons a {
                    background-color: #cd203c;display: block;padding: 18px;color: #fff;text-transform: uppercase;text-decoration: none;font-size: 17px;font-weight: 700;
                }
                .notification-buttons a:hover {
                    color: #fff;
                }
                @media (max-width: 1023px) {
                    .notification {
                        width: 100%;
                    }
                    .text-notification {
                        width: 100%;margin: 25px 0 0 0;
                    }
                }
                @keyframes dropdown {
                    100% {
                        transform: translate(0, 9em);
                        opacity: 0;
                        display: none;
                    }
                }
            </style>
            <div class="notification">
                <div class="text-notification">
                    Услуги call-центра от 6 руб. за минуту разговора.
                    <div class="small-text-notification">
                        Наш колл-центр работает с проектами любой сложности и всегда находит самое выгодное и эффективное решение для заказчика.<br>
                        Мы за индивидуальный подход!
                    </div>
                </div>
                <div class="notification-buttons">
                    <a class="btn btn_primary js-open-modal-ajax" data-target="feedbackForm" href="#" data-url="/firm-feedback/get-feedback-form/38352/10/?id_option=17" rel="nofollow" >Закажите консультацию</a>
                </div>
                <span class="close-notification">+</span>
            </div>
        <?}?>
    <?}?>
    <?if($advert_age_restrictions){?><p>Категория: <?=$advert_age_restrictions?></p><?}?>
    </div>
<?} else {?>
    <div class="cat_description">
        <h1>Запрашиваемая Вами информация не найдена</h1>
        <p>Возможно, запрашиваемая Вами информация была перенесена или удалена. Воспользуйтесь ссылками для перехода в другие разделы каталога.</p>
    </div>
        <?if($advert_age_restrictions){?><p>Категория: <?=$advert_age_restrictions?></p><?}?>
        <?= app()->chunk()->set('filter', [])->set('items', $no_items_groups)->set('id_group', $group->id())->render('common.catalog_tags')?>

    <div class="cat_description">
        <p>Пожалуйста, воспользуйтесь навигацией или формой поиска, чтобы найти интересующую Вас информацию</p>
    </div>
    <br/>
    <div class="pre_footer_adv_block">
            <?=app()->chunk()->render('adv.bottom_banners')?>
    </div>
    <?=$advert_restrictions?>

<?}?>
    <?
    if (isset($position['bottom'])){
        echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
    }
    ?>

</div>