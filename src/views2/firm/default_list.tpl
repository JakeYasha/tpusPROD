<?= $bread_crumbs?>
<div class="cat_description">
	<h1><?= $header?></h1>
	<?= $text?>
</div>
<div class="pre_footer_adv_block">
	<?=app()->chunk()->render('adv.catalog_top_banners')?>
</div>
<div class="search-result">
	<? if ($has_results) {?>
		<?= $tabs?>
		<div class="search-result-content">
			<?= $items?>
			<?= $pagination?>
		</div>
	<? } else {?>
		<div class="cat_description">
			<p>На текущий момент мы не располагаем статистическими данными по данному городу</p>
		</div>
	<? }?>
</div>