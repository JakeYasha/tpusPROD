<?= $bread_crumbs?>
<? if ($has_results) {?>
	<div class="cat_description">
		<h1>Результаты поиска по запросу &QUOT;<?= encode($query)?>&QUOT;</h1>
		<?= $text?>
	</div>
	<div class="search_result" style="border-top: none; margin-top: 0px;">
		<?= $tabs?>
		<?=app()->chunk()->render('adv.top_banners')?>
	</div>
<? } else {?>
	<div class="for_clients clearfix">
		<div class="for_clients_text_c clearfix page">
			<?= $text?>
		</div>	
	</div>
<? }?>
<div class="item_info">
	<div class="search-result">
		<div class="search-result-content">
			<div class="firm-bottom-block">
				<? if ($has_results) {?>
					<? if ($prices || $price_catalogs) {?>
						<div class="red-block">Результаты поиска по названиям товаров и услуг</div>
						<?if($prices){?>
						<div class="attention-info">
							<div>В результат поиска попали товары и услуги в названии которых было найдено вхождение слов поискового запроса &QUOT;<?= encode($query)?>&QUOT;. Сортировка списка идет по релевантности с учетом качества вхождений слов запроса (встретилось в начале названия или нет, полное совпадение или с учетом склонения), далее учитывается наличие изображения, цены, расширенного описания у предложения и даты обновления информации по предложению.</div>
						</div>
						<?= $prices?>
						<?if($prices_total_found > 0){?>
						<div class="firm_field" style="margin-top: 0">
							<div class="button_set" style="width: 100%;">
								<a class="get-price royal" style="margin: 0 auto;" href="<?= app()->linkFilter(app()->link('/search/prices/'), $filters)?>">Посмотреть еще <?=$prices_total_found?> <?=  \CWord::ending($prices_total_found, ['предложение','предложения','предложений'])?></a>
							</div>
						</div>
						<?}?>
						<?}?>
						<?= $price_catalogs?>
					<? }?>
				<? }?>
				<?=app()->chunk()->render('adv.middle_banners')?>
				<? if ($firms || $firm_catalogs) {?>
					<div class="red-block">Результаты поиска по названиям фирм и организаций</div>
					<?= $firm_catalogs?>
					<div class="attention-info">
						<div>В результат поиска попали фирмы для которых было найдено вхождение слов поискового запроса &QUOT;<?= encode($query)?>&QUOT; в названии, кратком описании вида деятельности, адресе или телефоне фирмы. Сортировка списка идет по релевантности с учетом количества вхождений слов запроса.</div>
					</div>
					<?$presenter = new \App\Presenter\FirmItems();$presenter->setForceHideActivity(true);
					foreach ($firms as $firm) {?>
						<?= app()->chunk()->set('item', $firm)->set('show_rating', false)->set('force_hide', true)->set('presenter', $presenter)->render('firm.chunk_firm_list_element')?>
					<? }?>
					<?if($firms_total_found > 0){?>
					<div class="firm_field" style="margin-top: 0">
						<div class="button_set" style="width: 100%;">
							<a class="get-price royal" style="margin: 0 auto;" href="<?= app()->linkFilter(app()->link('/search/firms/'), $filters)?>">Посмотреть еще <?=$firms_total_found?> <?=  \CWord::ending($firms_total_found, ['фирму','фирмы','фирм'])?></a>
						</div>
					</div>
					<?}?>
				<?}?>
			</div>
		</div>
	</div>
</div>
<?= app()->adv()->renderRestrictions()?>
<?= app()->adv()->renderAgeRestrictions()?>