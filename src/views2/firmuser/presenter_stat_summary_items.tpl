<div class="attention-info"  style="line-height: 17px;">
	<div>Отчет "Обзор" показывает сколько раз посетители посмотрели разные типы страниц сайта TovaryPlus.ru с упоминанием Вашей информации или совершили конкретное действие.</div>
	<div>Статистическая информация на сайте хранится в течении 6 месяцев.</div>
	<div>
		<b style="color:red;">ВНИМАНИЕ!!!</b> C 1/09/2023 идет работа над доработкой статистики. Ранее, с 1.01.2023 статистика могла вестись не верно из-за блокировок со стороны стороннего сервиса. Статистика "до" и "сейчас" - может отличаться. В течении месяца, будет переход на новый сервис замера статистики и отчёты будут полностью функционировать. <b>ВАЖНО!!!</b> <i>Если у фирмы нет товаров, но в статистике указывается "Показ карточек товара", это значит что во время поиска по товарам, фирма выводилась в поиске, т.е = "Показ фирмы в списке фирм при поиске(товаров в том числе) на сайте". Более точная статистика будет после разбора логов статистики.</i> Спасибо за понимание. С Уважением, Т+.
	</div>
</div>

<?if($items){?>
<div class="gray-block">Показ краткой информации о фирме в списках на страницах рубрик каталогов</div>
<table class="default-table summary-table additional_stat">
	<tr>
		<th class="summary-table-action">Действие</th>
		<th>Количество</th>
	</tr>
<? $additional_sum=0; foreach ($items as $item) { if ($item['stat_group'] == 'additional_stat') { $additional_sum += ceil($item['count']*STAT_ADD_COUNT); ?>
	<tr>
		<td><?=$item['name']?></td>
		<td><?=ceil($item['count']*STAT_ADD_COUNT)?></td>
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
<? $main_sum=0; foreach ($items as $item) { if ($item['stat_group'] == 'main_stat') { $main_sum += ceil($item['count']*STAT_ADD_COUNT); ?>
	<tr>
		<td><?=$item['name']?></td>
		<td><?=ceil($item['count']*STAT_ADD_COUNT)?></td>
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