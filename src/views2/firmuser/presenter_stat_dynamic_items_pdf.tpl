<div class="delimiter-block"></div>
<h2>Динамика посещений *</h2>
<table>
	<tr>
		<?  foreach ($items['cols'] as $col) {?>
		<th><?=$col?></th>
		<?}?>
	</tr>
<?foreach ($items['data'] as $item) {?>
	<tr>
		<td><?=$item['name']?></td>
		<td><?=ceil($item['users']*STAT_ADD_COUNT)?></td>
		<td class="last"><?=ceil($item['shows']*STAT_ADD_COUNT);?></td>
	</tr>
<?}?>
</table>
<div class="delimiter-block"></div>
<div class="attention-info" style="font-size: 0.85em;">
	<div>* В отчете "Динамика посещений" показывается изменение количества посетителей сайта TovaryPlus.ru и просмотренных ими страниц в разрезе отчетных периодов. Для выборки берутся данные из статистики по просмотренным страницам сайта TovaryPlus.ru, где была упомянута информация о вашей компании, и на ее основе вычисляется количество уникальных посетителей и число просмотров страниц для них.</div>
</div>