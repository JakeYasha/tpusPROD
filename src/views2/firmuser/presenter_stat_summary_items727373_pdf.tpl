<div class="delimiter-block"></div>
<h1>Cтатистика сайта 727373.ru</h1>
<div class="attention-info" style="font-size: 0.85em;">
	<div>Онлайн справочная Ярославля 727373.ru является интернет-сервисом Справочной службы 727373 Информационного центра "Товары плюс". Операторы справочной принимают и обрабатывают онлайн-запросы посетителей сайта 727373.ru в режиме чата и предоставляют аналогичную информационную поддержку, как и при обработке звонка на горячую линию справочной службы.<br />
        Сформированные ответы по запросам посетителей остаются на сайте 727373.ru и служат справочно-информационным материалом для других посетителей сайта и пользователей интернет. При этом текст запроса посетителя становится заголовком соответствующей страницы сайта.</div>
</div>
<div class="delimiter-block"></div>
<h2>Показ краткой информации о фирме</h2>
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