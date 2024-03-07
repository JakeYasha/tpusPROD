<?= $bread_crumbs?>
<? if ($has_results) {?>
    <div class="block-title">
        <div class="block-title__decore"></div>
        <h1 class="block-title__heading block-title__heading_h1"><?= $title?></h1>
    </div>
   <?if (app()->sidebar()->getParam('top_brands')) {?>
        <div class="block-title brand-list__actions_mobile">
            <div class="block-title__decore"></div>
            <h2 class="block-title__heading">Бренды</h2>
        </div>
        <div class="filter-list brand-list__actions_mobile">
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
                <label class="filter-list__item">
                    <input class="filter-list__radio" <?=(in_array($brand['id'], $brands_active) ? 'checked="checked"' : '')?> type="radio" name="brand">
                    <div class="filter-list__check"><a style="text-decoration: none;color: #34404e;" href="<?= app()->link(app()->linkFilter($url, array_merge($filters, ['page' => null]), ['brand' => $brand['id']]))?>"><?=str()->firstCharToUpper(str()->toLower($brand['site_name']))?></a></div>
                </label>
            <?}?>
        </div>
    <?}?>
		<?= $tabs?>
        <?= $items?>
        <?= $pagination?>
            <div class="module stat-info_v2">
                <p class="brand-list__item--text">В результат поиска попали товары и услуги в названии которых было найдено вхождение слов поискового запроса &QUOT;<?= encode(preg_replace('~[()]~', '', str()->replace($query, '|', ', ')))?>&QUOT;. Сортировка списка идет по релевантности с учетом качества вхождений слов запроса (полное совпадение фразы с названием товара или услуги, двойное вхождение в название одного из слов запроса, одно из слов запроса встретилось в начале названия или нет, в названии присутствует слова запроса с учетом склонения), далее учитывается наличие расширенного описания+изображения+цены, цены, рейтинг фирмы, далее алфавитная сортировка, после дата обновления информации по предложению.</p>
			</div>
		<? if ($filters['mode'] === null) {?>
			<?= $price_catalogs?>
		<? }?>
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
<br/>
<div class="pre_footer_adv_block">
		<?=app()->chunk()->render('adv.bottom_banners')?>
</div>