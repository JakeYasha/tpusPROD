<?if($items['data']){?>
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
		<td><?=$item['users']?></td>
		<td class="last"><?=$item['shows']?></td>
	</tr>
<?}?>
</table>
<div class="delimiter-block"></div>
<div class="attention-info" style="font-size: 0.85em;">
	<div>* В отчете "Динамика посещений" показывается изменение количества посетителей сайта 727373.ru и просмотренных ими страниц в разрезе отчетных периодов. Для выборки берутся данные из статистики по просмотренным страницам сайта 727373.ru, где была упомянута информация о вашей компании, и на ее основе вычисляется количество уникальных посетителей и число просмотров страниц для них.</div>
</div>
<?}?>