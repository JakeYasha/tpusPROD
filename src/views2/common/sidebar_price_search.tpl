<div class="side_bar index_side_bar filter-sidebar">
	<?=app()->chunk()->render('common.header_tp_logo');?>
	<div class="js-sidebar-town-holder top_menu_item">
		<a class="cms_tree_current name" href="#"><?= app()->location()->currentName()?></a>
		<? if (!empty($topCities)) {?>
			<ul>
				<?
				$i = 0;
				$current = app()->location()->city()->id();
				foreach ($topCities as $city) {
					if ($current == $city->id()) continue;$i++;
					?>
					<li<? if ($i == 1) {?> class="cms_tree_first"<? }?>><!--noindex--><a rel="nofollow" href="<?= app()->location()->changeLink($city->locationId())?>"><?= $city->name()?></a><!--/noindex--></li>
				<? }?>
				<li class="cms_tree_last">
					<label for="sidebar-town-autocomplete" class="another">Другой город:<br/>
						<?= $autocomplete?>
					</label>
				</li>
			</ul>
		<? }?>
	</div>
	<div class="back_menu">
		<ul class="catalog_menu">
			<?if($price_subgroups){?>
			<li class="catalogs-prices">
				<div class="title"><a href="#">Рубрики каталога</a></div>
				<ul class="js-sidebar-price-catalogs">
					<li><a href="<?=app()->linkFilter($url, $filters, ['id_catalog' => false])?>">Все разделы</a>
					<?$i=0;$count = count($price_subgroups);foreach ($price_subgroups as $id_parent => $childs) {$i++;?>
					<li<?if($i>3){?> class="js-sidebar-price-catalogs-elems"<?}?>><span><?= $childs['name']?></span>
						<?if($childs['items']){?>
						<ul>
							<? foreach ($childs['items'] as $val) {?>
							<li><a<?if((int)$filters['id_catalog'] === (int)$val['id_subgroup']){?> class="active"<?}?> href="<?=$val['link']?>"><?=$val['name']?>&nbsp;(<?=$val['count']?>)</a></li>
							<? }?>
						</ul>
						<?}?>
					</li>
					<?}?>
					<?if($count > 3) {?>
					<li class="sidebar-show-more"><a href="#" class="js-action js-sidebar-price-catalog-show-more">показать все</a></li>
					<?}?>
				</ul>
			</li>
			<?}?>
			<?if($wholesail_and_retail){?>
			<li>
				<div class="title"><a rel="nofollow" href="#">Тип продажи</a></div>
				<div class="cat_cont">
					<ul class="tabs_c">
						<?if(!$filters['price_type']){?>
						<li class="cms_tree_current"><span>Все</span></li>
						<?} else {?>
						<li><a href="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['price_type' => false]))?>">Все</a></li>
						<?}?>
						<?if($filters['price_type'] === 'retail'){?>
						<li class="cms_tree_current"><span>Розница</span></li>
						<?} else {?>
						<li><a href="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['price_type' => 'retail']))?>" rel="nofollow" >Розница</a></li>
						<?}?>
						<?if($filters['price_type'] === 'wholesale'){?>
						<li class="cms_tree_current"><span>Опт</span></li>
						<?} else {?>
						<li><a href="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['price_type' => 'wholesale']))?>" rel="nofollow">Опт</a></li>
						<?}?>
					</ul>
				</div>
			</li>
			<?}?>
			<? if ($brands) {?>
				<li>
					<div class="title"><a rel="nofollow" href="#">Производитель<?if(array_filter($brands_active)){?><sup><?=count($brands_active)?></sup><?}?></a></div>
					<div class="cat_cont">
						<ul class="manufacturer">
							<? $unique_brand_names = [];
								foreach ($brands as $brand) {
                                    if (in_array(str()->firstCharToUpper(str()->toLower($brand['site_name'])), $unique_brand_names)) {
                                        continue;
                                    } else {
                                        $unique_brand_names []= str()->firstCharToUpper(str()->toLower($brand['site_name']));
                                    }
                                    $current_brands_active = $brands_active;
                                    unset($current_brands_active[array_search($brand['id'], $current_brands_active)]);?>
                                <li><label><input<? if (in_array($brand['id'], $brands_active)) {?> checked="checked"<? }?> type="checkbox" class="js-location" data-url="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['brand' => in_array($brand['id'], $brands_active) ? ($current_brands_active ? implode(',', array_filter($current_brands_active)) : false) : implode(',', array_filter(array_merge($brands_active, [$brand['id']])))]))?>"><?= str()->firstCharToUpper(str()->toLower($brand['site_name']))?></label></li>
							<?}?>
						</ul>
					</div>
				</li>
			<? }?>
			<? if ($min_cost && $max_cost) {?>
				<li>
					<div class="title"><a rel="nofollow" href="#">Цена</a></div>
					<div class="cat_cont">
						<div class="price_slider">
							<label ><input<? if ($filters['with-price']) {?> checked="checked"<? }?> type="checkbox" class="js-location" data-url="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['with-price' => $filters['with-price'] ? false : '1']))?>">Только с ценами</label>
							<?if(1){
							if($filters['prices']) {
								$prices = explode(',', $filters['prices']);
							}
							?>
							<input type="text" id="amount_min" value="<?= $min_cost?>" data-current="<?=  isset($prices[0]) ? (int)$prices[0] : $min_cost?>"><span class="amount_after">до</span>
							<input type="text" id="amount_max" value="<?= $max_cost?>" data-current="<?=  isset($prices[1]) ? (int)$prices[1] : $max_cost?>"><span class="amount_after">руб.</span>
							<div id="price_slider"></div>
							<div class="button_set"><a rel="nofollow" href="#" class="simple js-location js-filter-amount-range js-block-event" data-url="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['prices' => false]))?>" >Применить</a></div>
							<br/><br/><br/>
							<?}?>
						</div>
					</div>
				</li>
			<? }?>
			<?if($price_cities){?>
			<li class="catalogs-prices catalogs-prices-city">
				<div class="title"><a href="#">География</a></div>
				<ul>
					<li><a href="<?=app()->linkFilter($url, $filters, ['id_city' => false])?>">Все города</a>
					<?foreach ($price_cities as $city) {?>
					<li><a<?if((int)$filters['id_city'] === (int)$city['id_city']){?> class="active"<?}?> href="<?=$city['link']?>"><?=$city['name']?>&nbsp;(<?=$city['count']?>)</a>
					<?}?>
				</ul>
			</li>
			<?}?>
		</ul>
		<?=app()->chunk()->render('adv.left_banners')?>
	</div>
</div>