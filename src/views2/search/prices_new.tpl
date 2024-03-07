<?= $bread_crumbs?>
<? if ($has_results) {?>
	<div class="cat_description">
		<h1><?= $title?></h1>
        <?if (app()->sidebar()->getParam('top_brands')) {?>
            <div class="brand-rubrics">
                Брэнды:
                <ul class="brand-rubrics-list clearfix">
                    <?
                        $top_brands = app()->sidebar()->getParam('top_brands');
                        $brands_active = app()->sidebar()->getParam('brands_active');
                        $url = app()->url();
                    ?>
                    <?$unique_brand_names = [];
                    foreach($top_brands as $brand) {
                        if (in_array(str()->firstCharToUpper(str()->toLower($brand['site_name'])), $unique_brand_names)) {
                            continue;
                        } else {
                            $unique_brand_names []= str()->firstCharToUpper(str()->toLower($brand['site_name']));
                        }?>
                        <li class="brand-rubrics-list-item <?=(in_array($brand['id'], $brands_active) ? 'active' : '')?>"><a href="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['brand' => $brand['id'], 'mode' => 'firm']))?>"><?=str()->firstCharToUpper(str()->toLower($brand['site_name']))?></a></li>
                    <?}?>
                </ul>
            </div>
        <?}?>
	</div>
	<div class="search-result">
		<?= $tabs?>
		<div class="search-result-content">			
			<?= $items?>
			<?= $pagination?>
			<div class="attention-info">
				<div>В результат поиска попали товары и услуги в названии которых было найдено вхождение слов поискового запроса &QUOT;<?= encode(preg_replace('~[()]~', '', str()->replace($query, '|', ', ')))?>&QUOT;. Сортировка списка идет по релевантности с учетом качества вхождений слов запроса (полное совпадение фразы с названием товара или услуги, двойное вхождение в название одного из слов запроса, одно из слов запроса встретилось в начале названия или нет, в названии присутствует слова запроса с учетом склонения), далее учитывается наличие расширенного описания+изображения+цены, цены, рейтинг фирмы, далее алфавитная сортировка, после дата обновления информации по предложению.</div>
			</div>
		</div>
		<? if ($filters['mode'] === null) {?>
			<?= $price_catalogs?>
		<? }?>
	</div>
	<?= app()->adv()->renderRestrictions()?>
	<?= app()->adv()->renderAgeRestrictions()?>
<? } else {?>
	<div class="for_clients clearfix">
		<div class="for_clients_text_c clearfix page">
			<?= $text?>
		</div>	
	</div>
	<div class="search-result">
		<?= $tabs?>
	</div>
	<?
}?>

<div class="pre_footer_adv_block">
		<?=app()->chunk()->render('adv.bottom_banners')?>
</div>