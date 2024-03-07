<?= $bread_crumbs?>
<div class="cat_description">
	<h1><?= $header?></h1>
	<?= $text?>
</div>
<?= app()->chunk()->render('adv.catalog_top_banners')?>
<div class="search_result">
	<? if ($has_results) {?>
		<?= $tabs?>
		<div class="search_result_content">
			<?= $items?>
			<?= $pagination?>
		</div>
	<? } else {?>
		<div class="cat_description">
			<p>На текущий момент мы не располагаем статистическими данными по данному городу</p>
		</div>
	<? }?>
</div>