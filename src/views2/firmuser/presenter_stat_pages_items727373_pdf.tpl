<?if($items){?>
<div class="delimiter-block"></div>
<div class="delimiter-block"></div>
<h2>Просмотренные страницы *</h2>
<table class="default-table pages-table">
	<tr>
		<th>Страница</th>
		<th class="last">Просмотры</th>
	</tr>
<?foreach ($items as $item) { if (!$item['name']) continue; ?>
	<tr>
		<td><a href="http://www.727373.ru<?=$item['url']?>"><?=$item['name']?></a></td>
		<td class="last"><?=$item['count']?></td>
	</tr>
<?}?>
</table>
<div class="delimiter-block"></div>
<div class="attention-info" style="font-size: 0.85em;">
	<div>* В отчете "Просмотренные страницы" показывается сколько раз за отчетный период и какие страницы сайта 727373.ru c ответами операторов просмотрены посетителями, где упоминалась информация о Вашей фирме.</div>
</div>
<?}?>