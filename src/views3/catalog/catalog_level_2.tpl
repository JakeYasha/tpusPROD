<div class="mdc-layout-grid">
    <?
    if (isset($position['top'])){
        echo app()->chunk()->set('position', $position['top'])->render('advtext.top_advtext');
    }
    ?>
    <?= $bread_crumbs?>
    <div class="block-title">
        <div class="block-title__decore"></div>
        <h1 class="block-title__heading block-title__heading_h1"><?=$title?></h1>
    </div>
    
    <p>На страницах каталога товаров "<?=$group->name()?>" вы найдете рекламные и справочно-информационные материалы и сведения о фирмах <?=app()->location()->currentName('prepositional')?>, а также предложения товаров, относящихся к тематике каталога. Торговые и другие отраслевые компании представляют к вашему вниманию свои прайс-листы, цены, фото и видеоматериалы на товары категории - <?=$group->name()?>.
    <p>Для просмотра интересующих вас предложений, выберите соответствующую рубрику.</p>
    
    <?=$promo?>
    
    <?if($group->exists() && $group->val('text1')){?><p><?=app()->metadata()->replaceLocationTemplates($group->val('text1'))?></p><?}?>
    
    <h2>Список категорий каталога товаров - <?=$group->name()?></h2>
    
    <?if($matrix && $id_group) {?>
        <?$sorted_groups = [];foreach ($matrix as $id_subgroup => $id_childs) {$sorted_groups[$items[$id_subgroup]['name']] = ['items' => $id_childs, 'id_subgroup' => $id_subgroup];}?>
        <? ksort($sorted_groups);foreach ($sorted_groups as $name => $childs_gr) {$id_childs = $childs_gr['items']; $id_subgroup = $childs_gr['id_subgroup'];?>
            <div class="module stat-info_v2 brand-list__actions_desktop">
                <div class="block-title">
                    <h3 class="block-title__heading">
                        <a style="text-decoration: none;color: #34404e;" href="<?= app()->link('/catalog/'.$id_group.'/'.$id_subgroup.'/')?>"><?=$items[$id_subgroup]['name']?></a>
                    </h3>
                </div>
                <?if($id_childs) {?>
                    <div class="mdc-layout-grid__inner">
                        <?$sorted = [];foreach ($id_childs as $id_child) {$child = $childs[$id_child];$sorted[$child->name()] = $id_child;}?>
                        <?ksort($sorted);$_i=1;foreach ($sorted as $name => $id) {$child = $childs[$id];?>
                            <?if ($_i !== 10) {?>
                                <div class="mdc-layout-grid__cell">
                                    <a class="service-item" href="<?=app()->link($child->link())?>"><?= $child->name()?></a>
                                </div>
                            <?} else {?>
                                <div class="mdc-layout-grid__cell--span-12">
                                    <div class="filter-form__box_hidden">
                                        <div class="mdc-layout-grid__inner">
                                            <div class="mdc-layout-grid__cell">
                                                <a class="service-item" href="<?=app()->link($child->link())?>"><?= $child->name()?></a>
                                            </div>
                            <? }?>
                        <?$_i++;}?>
                        <?if ($_i > 10) {?>
                                        </div>
                                    </div>
                                    <div class="filter-form__expand" id="service-expand-<?=$id_subgroup?>" style="margin-bottom: 1rem"><span>Показать все</span><img alt="" src="/img3/arrow-bot.png"></div>
                                </div>
                        <?}?>
                    </div>
                <?}?>
            </div>
            <div class="module stat-info_v2 brand-list__actions_mobile brand-list__actions_mobile_block">
                <div class="block-title">
                    <h3 class="block-title__heading">
                        <a style="text-decoration: none;color: #34404e;" href="<?= app()->link('/catalog/'.$id_group.'/'.$id_subgroup.'/')?>"><?=$items[$id_subgroup]['name']?></a>
                    </h3>
                </div>
                <?if($id_childs) {?>
                    <div class="mdc-layout-grid__inner">
                        <?$sorted = [];foreach ($id_childs as $id_child) {$child = $childs[$id_child];$sorted[$child->name()] = $id_child;}?>
                        <?ksort($sorted);$_i=1;foreach ($sorted as $name => $id) {$child = $childs[$id];?>
                            <?if ($_i !== 4) {?>
                                <div class="mdc-layout-grid__cell">
                                    <a class="service-item" href="<?=app()->link($child->link())?>"><?= $child->name()?></a>
                                </div>
                            <?} else {?>
                                <div class="mdc-layout-grid__cell--span-12">
                                    <div class="filter-form__box_hidden">
                                        <div class="mdc-layout-grid__inner">
                                            <div class="mdc-layout-grid__cell">
                                                <a class="service-item" href="<?=app()->link($child->link())?>"><?= $child->name()?></a>
                                            </div>
                            <? }?>
                        <?$_i++;}?>
                        <?if ($_i > 4) {?>
                                        </div>
                                    </div>
                                    <div class="filter-form__expand" id="service-expand-<?=$id_subgroup?>" style="margin-bottom: 1rem"><span>Показать все</span><img alt="" src="/img3/arrow-bot.png"></div>
                                </div>
                        <?}?>
                    </div>
                <?}?>
            </div>
        <?}?>
        <?if($group->exists() && $group->val('text2')){?><div class="cat_description"><?=app()->metadata()->replaceLocationTemplates($group->val('text2'))?></div><?}?>
    <?} else {?>
            <div class="cat_description">
                <p>К сожалению, на данный момент, мы не располагаем структурированным каталогом товаров для этого города. Для поиска интересующей Вас информации по другим городам воспользуйтесь выбором города.</p>
            </div>
    <? }?>
    <br/>

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

    <div class="pre_footer_adv_block">
    <?=app()->chunk()->render('adv.bottom_banners')?>
    </div>
    <?
    if (isset($position['bottom'])){
        echo app()->chunk()->set('position', $position['bottom'])->render('advtext.bottom_advtext');
    }
    ?>


</div>
