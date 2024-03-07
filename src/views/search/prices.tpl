<?= $bread_crumbs?>
<?if($has_results){?>
	<div class="cat_description">
		<div class="red-block">Поиск по названиям товаров и услуг</div>
		<h1 style="padding-bottom: 0">Результаты поиска по запросу &QUOT;<?= encode($query)?>&QUOT;</h1>
		<div class="attention-info">
								<div>В результат поиска попали товары и услуги в названии которых было найдено вхождение слов поискового запроса &QUOT;<?= encode($query)?>&QUOT;. Сортировка списка идет по релевантности с учетом качества вхождений слов запроса (полное совпадение фразы с названием товара или услуги, двойное вхождение в название одного из слов запроса, одно из слов запроса встретилось в начале названия или нет, в названии присутствует слова запроса с учетом склонения), далее учитывается наличие расширенного описания+изображения+цены, цены, рейтинг фирмы, далее алфавитная сортировка, после дата обновления информации по предложению.</div>
		</div>
	</div>
<? } else {?>
	<div class="for_clients clearfix">
		<div class="for_clients_text_c clearfix page">
			<?=$text?>
		</div>	
	</div>
<?}?>
<div class="search_result">
	<?= $tabs?>
</div>
<div class="item_info">
	<div class="search_result">
		<div class="search_result_content">
			<div class="firm-bottom-block">
				<? if ($has_results) {?>
					<?= $items?>
					<?= $pagination?>
					<?if($filters['mode'] === null){?>
						<?=$price_catalogs?>
					<?}?>
				<?}?>
			</div>
		</div>
	</div>
</div>
<?=app()->adv()->renderRestrictions()?>
<?=app()->adv()->renderAgeRestrictions()?>