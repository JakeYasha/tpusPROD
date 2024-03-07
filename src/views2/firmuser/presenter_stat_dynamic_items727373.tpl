<div class="attention-info" style="line-height: 17px;">
	<div>В отчете "Динамика посещений" показывается изменение количества посетителей сайта 727373.ru и просмотренных ими страниц в разрезе отчетных периодов. Для выборки берутся данные из статистики по просмотренным страницам сайта 727373.ru, где была упомянута информация о вашей компании, и на ее основе вычисляется количество уникальных посетителей и число просмотров страниц для них.</div>
	<div>Статистическая информация на сайте хранится в течении 6 месяцев.</div>
</div>
<?if($items){?>
<?=  app()->chunk()->set('items', $items['chart_items'])->set('title', '')->set('width', '60%')->render('charts.line')?>
<table class="default-table pages-table">
	<tr>
		<?  foreach ($items['cols'] as $col) {?>
		<th><?=$col?></th>
		<?}?>
	</tr>
<?foreach ($items['data'] as $item) {?>
	<tr>
		<td><?=$item['name']?></td>
		<td><?=$item['users']?></td>
		<td><?=$item['shows']?></td>
	</tr>
<?}?>
</table>
<?} else {?>
<div class="cat_description">
	<p>Нет данных</p>
</div>
<?}?>