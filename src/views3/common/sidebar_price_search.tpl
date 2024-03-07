<style>
    .sidebar-center-next-div>div{
        text-align:center;
    }
</style>

<aside class="sidebar sidebar-center-next-div">
    <div class="filter-form">
        <?if($price_subgroups){?>
            <div class="block-title">
                <div class="block-title__decore"></div>
                <h2 class="block-title__heading">рубрики каталога</h2>
            </div>
            <div class="filter-form__box">
                <ul class="list sidebar-list">
                    <li class="sidebar-root-list__item">
                        <a class="sidebar-list__link" href="<?=app()->linkFilter($url, $filters, ['id_catalog' => false])?>">
                            Все разделы
                        </a>
                    </li>
                    <?$i=1;$count = count($price_subgroups);foreach ($price_subgroups as $id_parent => $childs) {?>
                        <? if($i !== 3) {?>
                            <li class="sidebar-root-list__item">
                                <?= $childs['name']?>
                                <?if($childs['items']){?>
                                    <ul class="list sidebar-list">
                                        <? foreach ($childs['items'] as $val) {?>
                                            <li class="sidebar-list__item">
                                                <a class="sidebar-list__link <?if((int)$filters['id_catalog'] === (int)$val['id_subgroup']){?> active<?}?>" href="<?=$val['link']?>">
                                                    <?=$val['name']?>&nbsp;(<?=$val['count']?>)
                                                </a>
                                            </li>
                                        <? }?>
                                    </ul>
                                <?}?>
                            </li>
                        <?} else {?>
                            <div class="filter-form__box_hidden">
                                <li class="sidebar-root-list__item">
                                    <?= $childs['name']?>
                                    <?if($childs['items']){?>
                                        <ul class="list sidebar-list">
                                            <? foreach ($childs['items'] as $val) {?>
                                                <li class="sidebar-list__item">
                                                    <a class="sidebar-list__link <?if((int)$filters['id_catalog'] === (int)$val['id_subgroup']){?> active<?}?>" href="<?=$val['link']?>">
                                                        <?=$val['name']?>&nbsp;(<?=$val['count']?>)
                                                    </a>
                                                </li>
                                            <? }?>
                                        </ul>
                                    <?}?>
                                </li>
                        <?}?>
                    <?$i++;}?>
                    <? if($i > 3) {?>
                        </div>
                        <div class="filter-form__expand" id="filter-form-expand"><span>Показать все</span><img alt="" src="/img3/arrow-bot.png"></div>
                    <?}?>
                </ul>
            </div>
        <?}?>
        <? if ($wholesail_and_retail) { ?>
            <div class="block-title">
                <div class="block-title__decore"></div>
                <h2 class="block-title__heading">тип продажи</h2>
            </div>
            <div class="filter-form__box">
                <label class="filter-form__iten">
                    <input class="filter-form__control filter-price_type" <? if (!$filters['price_type']) { ?>checked="checked"<?} else {?>data-url="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['price_type' => false])) ?>"<?}?> type="radio" name="type"/>
                    <span class="filter-form__radio"></span>
                    <span class="filter-form__label">ВСЕ</span>
                </label>
                <label class="filter-form__iten">
                    <input class="filter-form__control filter-price_type" <? if ($filters['price_type'] === 'retail') { ?>checked="checked"<?} else {?>data-url="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['price_type' => 'retail'])) ?>"<?}?> action type="radio" name="type"/>
                    <span class="filter-form__radio"></span>
                    <span class="filter-form__label">РОЗНИЦА</span>
                </label>
                <label class="filter-form__iten">
                    <input class="filter-form__control filter-price_type" <? if ($filters['price_type'] === 'wholesale') { ?>checked="checked"<?} else {?>data-url="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['price_type' => 'wholesale'])) ?>"<?}?> type="radio" name="type"/>
                    <span class="filter-form__radio"></span>
                    <span class="filter-form__label">ОПТ</span>
                </label>
            </div>
        <?}?>
        <? if ($brands) { ?>
            <div class="block-title">
                <h2 class="block-title__heading">производитель<? if (array_filter($brands_active)) { ?><sup><?= count($brands_active) ?></sup><? } ?></h2>
            </div>
            <div class="filter-form__box">
                <label class="filter-form__iten">
                    <input class="filter-form__control filter-form__control_checkbox js-location" type="checkbox" name="brand"><span class="filter-form__checkbox"></span><span class="filter-form__label">ВСЕ</span>
                </label>
                <?
                $unique_brand_names = [];
                $_i=1;
                foreach ($brands as $brand) {
                    if (in_array(str()->firstCharToUpper(str()->toLower($brand['site_name'])), $unique_brand_names)) {
                        continue;
                    } else {
                        $unique_brand_names [] = str()->firstCharToUpper(str()->toLower($brand['site_name']));
                    }
                    $current_brands_active = $brands_active;
                    unset($current_brands_active[array_search($brand['id'], $current_brands_active)]);
                    ?>
                    <? if($_i !== 6) {?>
                        <label class="filter-form__iten">
                            <input class="filter-form__control filter-form__control_checkbox js-location" <? if (in_array($brand['id'], $brands_active)) { ?> checked="checked"<? } ?> type="checkbox" name="brand" data-url="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['brand' => in_array($brand['id'], $brands_active) ? ($current_brands_active ? implode(',', array_filter($current_brands_active)) : false) : implode(',', array_filter(array_merge($brands_active, [$brand['id']])))])) ?>">
                            <span class="filter-form__checkbox"></span>
                            <span class="filter-form__label"><?= str()->firstCharToUpper(str()->toLower($brand['site_name'])) ?></span>
                        </label>
                    <?} else {?>
                        <div class="filter-form__box_hidden">
                            <label class="filter-form__iten">
                                <input class="filter-form__control filter-form__control_checkbox js-location" <? if (in_array($brand['id'], $brands_active)) { ?> checked="checked"<? } ?> type="checkbox" name="brand" data-url="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['brand' => in_array($brand['id'], $brands_active) ? ($current_brands_active ? implode(',', array_filter($current_brands_active)) : false) : implode(',', array_filter(array_merge($brands_active, [$brand['id']])))])) ?>">
                                <span class="filter-form__checkbox"></span>
                                <span class="filter-form__label"><?= str()->firstCharToUpper(str()->toLower($brand['site_name'])) ?></span>
                            </label>
                    <?}?>
                <? $_i++;} ?>
                <? if($_i > 6) {?>
                        </div>
                        <div class="filter-form__expand" id="filter-form-expand"><span>Показать все</span><img alt="" src="/img3/arrow-bot.png"></div>
                <?}?>
            </div>
        <?}?>
        <? if ($min_cost && $max_cost) {?>
            <div class="block-title">
                <h2 class="block-title__heading">цена</h2>
            </div>
            <div class="filter-form__box">
                <label class="filter-form__iten">
                    <input class="filter-form__control filter-form__control_checkbox js-location"<? if ($filters['with-price']) {?> checked="checked"<? }?> type="checkbox" name="only-price" data-url="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['with-price' => $filters['with-price'] ? false : '1']))?>"><span class="filter-form__checkbox"></span><span class="filter-form__label">ТОЛЬКО С ЦЕНАМИ</span>
                </label>
                <div class="form-filter__range-input">
                    <?if($filters['prices']) {
                        $prices = explode(',', $filters['prices']);
                    }?>
                    <input class="filter-form__input" id="amount_min" value="<?= $min_cost?>" data-current="<?=  isset($prices[0]) ? (int)$prices[0] : $min_cost?>"><span class="filter-form__input-info">ДО</span>
                    <input class="filter-form__input" id="amount_max" value="<?= $max_cost?>" data-current="<?=  isset($prices[1]) ? (int)$prices[1] : $max_cost?>"><span class="filter-form__input-info">РУБ</span>
                </div>
                <div class="filter-form__box">
                    <button class="btn btn_primary simple js-location js-filter-amount-range js-block-event" style="max-width: 100%; margin-top: 1.5rem" data-url="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['prices' => false]))?>">Применить</button>
                </div>
            </div>
        <? }?>
        <?if($price_cities){?>
            <div class="block-title">
                <div class="block-title__decore"></div>
                <h2 class="block-title__heading">География</h2>
            </div>
            <div class="filter-form__box">
                <ul class="list sidebar-list">
                    <li class="sidebar-list__item">
                        <a class="sidebar-list__link" href="<?=app()->linkFilter($url, $filters, ['id_city' => false])?>">
                            Все города
                        </a>
                    </li>
                    <?foreach ($price_cities as $city) {?>
                        <li class="sidebar-list__item">
                            <a  class="sidebar-list__link <?if((int)$filters['id_city'] === (int)$city['id_city']){?>active<?}?>" href="<?=$city['link']?>">
                                <?=$city['name']?>&nbsp;(<?=$city['count']?>)
                            </a>
                        </li>
                    <?}?>
                </ul>
            </div>
        <?}?>
		<?=app()->chunk()->render('adv.left_banners')?>
	</div>
	<div>
        <!-- Yandex.RTB R-A-63031-7 -->
        <div id="yandex_rtb_R-A-63031-7"></div>
        <script>
            (function (w, d, n, s, t) {
                w[n] = w[n] || [];
                w[n].push(function () {
                    Ya.Context.AdvManager.render({
                        blockId: "R-A-63031-7",
                        renderTo: "yandex_rtb_R-A-63031-7",
                        async: true
                    });
                });
                t = d.getElementsByTagName("script")[0];
                s = d.createElement("script");
                s.src = "//an.yandex.ru/system/context.js";
                s.async = true;
                t.parentNode.insertBefore(s, t);
            })(this, this.document, "yandexContextAsyncCallbacks");
        </script>
		<p> </p>
		<!-- Admitad Widget -->
		<script type='text/javascript'>(function() {
		  /* Optional settings (these lines can be removed): */ 
		   subID = "";  // - local banner key;
		   injectTo = "";  // - #id of html element (ex., "top-banner").
		  /* End settings block */ 

		if(injectTo=="")injectTo="admitad_shuffle"+subID+Math.round(Math.random()*100000000);
		if(subID=='')subid_block=''; else subid_block='subid/'+subID+'/';
		document.write('<div id="'+injectTo+'"></div>');
		var s = document.createElement('script');
		s.type = 'text/javascript'; s.async = true;
		s.src = 'https://ad.admitad.com/shuffle/f617521fce/'+subid_block+'?inject_to='+injectTo;
		var x = document.getElementsByTagName('script')[0];
		x.parentNode.insertBefore(s, x);
		})();</script>	
	</div>

</aside>