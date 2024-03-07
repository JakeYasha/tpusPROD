<?= $bread_crumbs?>
<div class="firm_field single_item">
	<div class="image_field">
		<div class="image"<?if($item['images']){?> style="height: 420px;"<?}?>>
			<? if ($item['image']) {?><a rel="nofollow" href="<?= $item['image']?>" class="fancybox" rel="price_images"><img src="<?=$item['image']?>" alt="<?=encode($item['name'])?>" /></a><? }?>
			<?/*todo<div class="to_fav"><a href="#">Добавить в избранное</a></div>*/?>
			<?if($item['images']){?>
			<div class="price-images">
				<ul>
					<? foreach ($item['images'] as $img){?>
					<li style="width:50px;height: 50px;display: inline-block;"><a style="width:100%;height:100%;" class="fancybox" href="<?=$item['images_base_path']?>/<?=$img->val('file_subdir_name').'/'.$img->val('file_name').'.'.$img->val('file_extension')?>" rel="price_images"><img src="<?=$item['images_base_path']?>/<?=$img->val('file_subdir_name').'/'.$img->val('file_name').'.'.$img->val('file_extension')?>" alt="<?=$item['name']?>" style="width:100%;" /></a></li>
					<?}?>
				</ul>
			</div>
			<?}?>
		</div>
	</div>
	<div class="description">
		<h1><?= $item['name']?></h1>
		<div class="top_review"></div>
		<? if ($item['price'] !== null) {?>
			<div class="price">
				<div class="cell">
					<?if($item['price']) {?>
						<span class="price_field"><?=$item['price']?> <?= $item['currency']?></span>
						<?if($item['price_wholesale'] && $item['price_retail']){?>
						<span class="price_field<?=($item['price_wholesale']) ? ' under_price' : ''?>"><?=$item['price_wholesale']?> <?= $item['currency']?><?=($item['price_wholesale']) ? '&nbsp; оптом' : ''?></span>
						<?}?>
					<?}?>
					<?if($item['unit']){?><span class="under_price unit">цена за <?= $item['unit']?></span><?}?>
				</div>
				<? if ($item['old_price']) {?><div class="old_price">
						<p>Старая цена:</p>
						<span><?= $item['old_price']?> <?= $item['currency']?>.</span>
					</div><? }?>
				
				<?=app()->chunk()->set('firm', $firm)->set('item', $item)->set('id', $item['id'])->render('common.button_set_price_big')?>
				
			</div>
		<? } else {?>
			<?=app()->chunk()->set('firm', $firm)->set('item', $item)->set('id', $item['id'])->set('style', 'float: left; width: 100%')->render('common.button_set_price_big')?>
		<? }?>
		<? if (str()->length($item['info']) > 2) {?><p><?= $item['info']?></p><? }?>
		<? if (str()->length($item['production']) > 2) {?><p>Производство: <strong><?=$item['production']?></strong><?if($item['pack']){?>, фасовка: <strong><?=$item['pack']?></strong><?}?></p><?} else {?><?if($item['pack']){?><p>Фасовка: <strong><?=$item['pack']?></strong></p><?}?><?}?>
		<? if (str()->length($item['info']) < 2 && str()->length($item['production']) < 2){?><p>&nbsp;</p><?}?>

		<div class="price_contact_field">
			<div class="man_desc">
				<a class="name_firm" href="<?= $firm->link()?>"><?= $firm->name()?></a>
				<p><?= $firm->activity()?></p>
				<br/>
				<?if ($firm_catalog_analog_prices_count > 1) {?>
					<p><a rel="nofollow" href="<?=$firm_catalog_analog_prices_url?>">Еще <?=$firm_catalog_analog_prices_count?> похожих предложений фирмы в рубрике "<?=$firm_catalog_name_analog_prices?>"</a></p><br/>
				<?}?>
				<p><a rel="nofollow" href="<?=$firm->link().'?mode=price'?>">Все товары и услуги фирмы [<?=$firm_all_prices_count?>]</a></p>
			</div>
			<div class="contacts">
				<div class="title">контакты:</div>
				<? if ($firm->hasPhone()) {?><p><span class="r tel"><?= $firm->phone()?></span></p><? }?>
				<p><?= $firm->address()?></p>
				<? if ($firm->hasWeb()) {?>
					<p><span class="r"><?
							$i = 0;
							$_i = count($firm->webSiteUrls());
							foreach ($firm->webSiteUrls() as $url) {
								$i++;
								?>
								<a rel="nofollow" target="_blank" href="<?= app()->away(trim($url), $firm->id())?>"><?=trim($url)?></a><? if ($_i !== $i) {?>, <? }?>
							<? }?>
						</span>
					</p>
				<? }?>
			</div>
		</div>
	</div>
</div>
<div class="item_info">
	<div class="search_result description">
		<p>За более полной информацией о <?=($item['id_group'] == 44 ? 'услуге' : 'товаре')?> <?=$item['name']?>, по вопросам заказа<?=($item['id_group'] == 44 ? ' услуги' : ', покупки и доставки товара')?>, пожалуйста, обращайтесь в фирму <?=$firm->name()?>. Актуальные цены <?=($item['id_group'] == 44 ? 'на услугу' : 'и наличие товара')?> на текущий момент вы можете узнать по телефону <?=app()->location()->currentName('prepositional')?>: <?=$firm->phone()?></p>
		<p>&nbsp;</p>
		<p>Ответственность за достоверность и актуальность информации по предложению, несет фирма предоставившая данную информацию в своем прайс-листе для размещения на сайте.</p>
		<p>&nbsp;</p>
	</div>
	<?= $price_on_map?>
	<?if($other_items||$additional_items){?><div class="black-block">Возможно вас заинтересуют:</div><?}?>
	<div class="search_result">
	<?if(strlen($firm_catalog_name_analog_prices) > 2) {?>
		<a href="<?=$current_catalog_link?>"><div class="red-block">Другие фирмы с предложениями в рубрике "<?=$firm_catalog_name_analog_prices?>"</div></a>
	<?}?>
		<?= $other_items?>
		<?= $additional_items?>
		<? if ($item['id_group'] != 44) {?>
			<div class="attention-info">
				<div>Описание и изображение товаров на сайте носят информационный характер и могут отличаться от фактического описания, технической документации от производителя и реального вида товаров. Рекомендуем уточнять наличие желаемых функций и характеристик товаров у продавца.</div>
			</div>
		<? }?>
                <?=app()->chunk()->render('adv.top_banners')?>
                <?=app()->chunk()->render('adv.middle_banners')?>
                <? if (count($price_parent_catalogs) > 0) {?>
                <div class="notice-gray-price-show">
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
	<div class="pre_footer_adv_block">
                <? if(strlen(app()->chunk()->render('adv.top_banners')) < 10 || strlen(app()->chunk()->render('adv.middle_banners')) < 10) { /* костыль!!! */ ?>
                        <?=app()->chunk()->render('adv.bottom_banners')?>
                <?}?>
	</div>
</div>
<?=$advert_restrictions?>
