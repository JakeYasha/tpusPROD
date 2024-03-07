<div class="delimiter-block"></div>
<h1>Cтатистика сайта TovaryPlus.ru</h1>
<h2>Показ краткой информации о фирме в списках на страницах рубрик каталогов</h2>
<table class="default-table summary-table additional_stat">
	<tr>
		<th class="summary-table-action">Действие</th>
		<th>Количество</th>
	</tr>
<? $additional_sum=0; foreach ($items as $item) { if ($item['stat_group'] == 'additional_stat') { $additional_sum += $item['count']; ?>
	<tr>
		<td><?=$item['name']?></td>
		<td class="last"><?=$item['count']?></td>
	</tr>
<?}}?>
<? if($additional_sum > 0) {?>
	<tr>
		<td><strong>Всего</strong></td>
		<td><strong><?=$additional_sum?></strong></td>
	</tr>
<?}?>        
</table>
<div class="delimiter-block"></div>
<h2>Показ персональных страниц фирмы</h2>
<table class="default-table summary-table main_stat">
	<tr>
		<th class="summary-table-action">Действие</th>
		<th>Количество</th>
	</tr>
<? $main_sum=0; foreach ($items as $item) { if ($item['stat_group'] == 'main_stat') { $main_sum += $item['count']; ?>
	<tr>
		<td><?=$item['name']?></td>
		<td class="last"><?=$item['count']?></td>
	</tr>
<?}}?>
<? if($main_sum > 0) {?>
	<tr>
		<td><strong>Всего</strong></td>
		<td><strong><?=$main_sum?></strong></td>
	</tr>
<?}?>        
</table>
<div class="delimiter-block"></div>	