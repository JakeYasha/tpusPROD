<div class="attention-info" style="line-height: 17px;">
	<div>На странице "Динамика посещений 1" представлены графики по динамике изменения количества числа посетителей сайта TovaryPlus.ru и просмотренных ими страниц с учетом месячной и недельной выборки данных. Для выборки берутся данные из статистики по просмотренным страницам сайта TovaryPlus.ru, где была упомянута информация о вашей компании, и на ее основе вычисляется количество уникальных посетителей и число просмотров страниц для них.</div>
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
		<td><?=ceil($item['users']*STAT_ADD_COUNT)?></td>
		<td><?=ceil($item['shows']*STAT_ADD_COUNT)?></td>
	</tr>
<?}?>
</table>
<?} else {?>
<div class="cat_description">
	<p>Нет данных</p>
</div>
<?}?>