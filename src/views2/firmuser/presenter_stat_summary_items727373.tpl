<div class="attention-info" style="line-height: 17px;">
	<div>Отчет "Обзор" показывает сколько раз посетители посмотрели разные типы страниц сайта 727373.ru с упоминанием Вашей информации или совершили конкретное действие.</div>
	<div>Статистическая информация на сайте хранится в течении 6 месяцев.</div>
</div>
<?if($items){?>
<div class="gray-block">Показ краткой информации о фирме</div>
<table class="default-table summary-table additional_stat">
	<tr>
		<th class="summary-table-action">Действие</th>
		<th>Количество</th>
	</tr>
<? $additional_sum=0; foreach ($items as $item) { if ($item['stat_group'] == 'additional_stat') { $additional_sum += $item['count']; ?>
	<tr>
		<td><?=$item['name']?></td>
		<td><?=$item['count']?></td>
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
<div class="gray-block">Показ персональных страниц фирмы</div>
<table class="default-table summary-table main_stat">
	<tr>
		<th class="summary-table-action">Действие</th>
		<th>Количество</th>
	</tr>
<? $main_sum=0; foreach ($items as $item) { if ($item['stat_group'] == 'main_stat') { $main_sum += $item['count']; ?>
	<tr>
		<td><?=$item['name']?></td>
		<td><?=$item['count']?></td>
	</tr>
<?}}?>
<? if($main_sum > 0) {?>
	<tr>
		<td><strong>Всего</strong></td>
		<td><strong><?=$main_sum?></strong></td>
	</tr>
<?}?>        
</table>

<?} else {?>
<div class="cat_description">
	<p>Нет данных</p>
</div>
<?}?>