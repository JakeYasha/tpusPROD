<div class="item_info  offset-none">
<div class="search-result">
	<?= $tabs?>
	<?= $inside_bread_crumbs?>
	<? if ($filters['id_catalog'] !== null || $filters['q'] !== null) {?>
		<div class="search_price_field">
			<form class="js-autocomplete-form" action="<?= $url?>" method="get"><?= $autocomplete?><input type="submit" value="" class="submit"/><input type="hidden" name="mode" value="price" /></form><?= app()->chunk()->set('filters', $filters)->set('link', $url)->render('common.display_mode')?><?= $sorting?>
		</div>
		<div class="firm-bottom-block" style="margin-top: 20px;">
			<h2><?= app()->metadata()->getHeader()?></h2>
			<? if ($total_founded !== 0) {?><div class="description mbp20"><p>Найдено подходящих предложений: <?= $total_founded?> из <?= $total_price_list_count?></p></div><? }?>
		</div>	
	<? } else {?>
		<div class="firm-bottom-block">
			<h2><?= app()->metadata()->getHeader()?></h2>
			<div class="search_price_field">
				<form class="js-autocomplete-form" action="<?= $url?>" method="get"><?= $autocomplete?><input type="submit" value="" class="submit"/><input type="hidden" name="mode" value="price" /></form>
			</div>
			<p>Всего предложений: <?= $total_price_list_count?></p>
		</div>	
		
	<? }?>

	<? if ($total_founded === 0 && ($filters['id_catalog'] !== null || $filters['q'] !== null)) {?>
		<div class="cat_description">
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
<div class="pre_footer_adv_block">
	<?= app()->chunk()->render('adv.bottom_banners')?>
</div>
<?=$advert_restrictions?>
</div>