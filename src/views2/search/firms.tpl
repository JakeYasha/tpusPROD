<?= $bread_crumbs?>
<? if ($has_results) {?>
	<div class="cat_description">
		<div class="red-block">Поиск по названиям фирм</div>
		<h1 style="padding-bottom: 0">Результаты поиска по запросу &QUOT;<?= encode($query)?>&QUOT;</h1>
		<div class="attention-info">
			<div>В результат поиска попали фирмы для которых было найдено вхождение слов поискового запроса &QUOT;<?= encode($query)?>&QUOT; в названии, кратком описании вида деятельности фирмы, адресе. Сортировка списка идет по релевантности с учетом количества вхождений слов запроса.</div>
		</div>
	</div>
<? } else {?>
	<div class="for_clients clearfix">
		<div class="for_clients_text_c clearfix page">
			<?= $text?>
		</div>	
	</div>
<? }?>
<div class="search-result">
	<?= $tabs?>
</div>
<div class="item_info">
	<div class="search-result">
		<div class="search-result-content">
			<div class="firm-bottom-block">
				<? if ($has_results) {?>
					<?= $pagination?>
					<?= $items?>
					<?= $pagination?>
					<?if($filters['mode'] === null){?>
						<?=$firm_catalogs?>
					<?}?>
				<? }?>
			</div>
		</div>
	</div>
</div>
<?= app()->adv()->renderRestrictions()?>
<?= app()->adv()->renderAgeRestrictions()?>

<div class="pre_footer_adv_block">
		<?=app()->chunk()->render('adv.bottom_banners')?>
</div>