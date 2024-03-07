<div class="attention-info" style="line-height: 17px;">
	<div>Отчет "География" показывает, сколько посетителей и из каких регионов просматривали страницы сайта TovaryPlus.ru, где упоминалась информация о Вашей фирме. В отчет выводится 10 регионов с максимальным количеством посетителей. <br />
		Определение региона идет на основе информации об ip адресе посетителя (провайдер, город и страна регистрации).</div>
	<div>Статистическая информация на сайте хранится в течении 6 месяцев.</div>
</div>
<?if($items['data']){?>
<?=  app()->chunk()->set('items', $items['chart_items'])->set('title', '')->set('width', '60%')->render('charts.donut')?>
<table class="default-table pages-table">
	<tr>
		<th>Город</th>
		<th>Посетители</th>
	</tr>
<?foreach ($items['data'] as $item) {?>
	<tr>
		<td><?=$item['name']?></td>
		<td><?=$item['count']?></td>
	</tr>
<?}?>
</table>
<?} else {?>
<div class="cat_description">
	<p>Нет данных</p>
</div>
<?}?>