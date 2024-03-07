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
                                    <?/*
					<div class="disc">
						<input<? if ($filters['discount']) {?> checked="checked"<? }?> type="checkbox" id="discounts" class="js-location" data-url="<?= app()->link(app()->linkFilter($url, $filters, ['discount' => $filters['discount'] ? false : 1]))?>"><label for="discounts"><?=$is_service ? 'Услуги со скидками' : 'Товары со скидками'?></label>
					</div>
                                    */?>
				</div>
			</li>
			<?} else {?>
			<li>
				<div class="cat_cont">
                                    <?/*
					<div class="disc">
						<input<? if ($filters['discount']) {?> checked="checked"<? }?> type="checkbox" id="discounts" class="js-location" data-url="<?= app()->link(app()->linkFilter($url, $filters, ['discount' => $filters['discount'] ? false : 1]))?>"><label for="discounts"><?=$is_service ? 'Услуги со скидками' : 'Товары со скидками'?></label>
					</div>
                                    */?>
				</div>
			</li>
			<?}?>
			<? if ($brands) {?>
				<li>
					<div class="title"><a rel="nofollow" href="#">Производитель<?if(array_filter($brands_active)){?><sup><?=count($brands_active)?></sup><?}?></a></div>
					<div class="cat_cont">
						<ul class="manufacturer">
							<? foreach ($brands as $brand) {?>
								<? $current_brands_active = $brands_active;
								unset($current_brands_active[array_search($brand['id'], $current_brands_active)]);?>
							<li><label><input<? if (in_array($brand['id'], $brands_active)) {?> checked="checked"<? }?> type="checkbox" class="js-location" data-url="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['brand' => in_array($brand['id'], $brands_active) ? ($current_brands_active ? implode(',', array_filter($current_brands_active)) : false) : implode(',', array_filter(array_merge($brands_active, [$brand['id']])))]))?>"><?= str()->firstCharToUpper(str()->toLower($brand['site_name']))?></label></li>
	<? }?>
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
		</ul>
		<? /* <!-- end catalog_menu -->
		  <div class="similar_items">
		  <h3>Похожите товары<br>
		  в других категориях</h3>
		  <a href="">Столы</a>
		  <a href="">Мягкая мебель</a>
		  <a href="">Шкафы</a>
		  </div> */?>
		<?=app()->chunk()->render('adv.left_banners')?>
	</div>
</div>