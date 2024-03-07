<div class="attention-info" style="line-height: 17px;">
	<div>Отчет "Страницы" показывает, какие страницы сайта TovaryPlus.ru и сколько раз были просмотрены посетителями TovaryPlus.ru, где была упомянута информация о Вашей фирме (Страницы товаров, рубрик каталога, страницы о фирме, прайс-лист, акции и т.п) <br />
	Если кликнуть по ссылке, то Вы увидите, какую именно информацию просматривали Ваши потенциальные покупатели на данных страницах.</div>
	<div>Статистическая информация на сайте хранится в течении 6 месяцев.</div>
</div>
<?if($items){?>
<table class="default-table pages-table stat_table">
	<tr>
		<th>Страница</th>
		<th>Просмотры</th>
	</tr>
<?foreach ($items as $item) {?>
	<tr class="<?=$item['stat_group']?>">
		<td><a href="<?=$item['url']?>"><?=$item['name']?></a></td>
		<td><?=$item['count']?></td>
	</tr>
<?}?>
</table>
<?=$pagination ?? ''?>
<?} else {?>
<div class="cat_description">
	<p>Нет данных</p>
</div>
<?}?>
