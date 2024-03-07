<?if($items){?>
<div class="delimiter-block"></div>
<div class="delimiter-block"></div>
<div class="delimiter-block"></div>
<h1>Статистика звонков *</h1>
<table>
	<tr>
		<th>Дата</th>
		<th>Наименование</th>
		<th>Производство</th>
		<th>Ед. изм.</th>
		<th>Переадресация</th>
	</tr>
<?$i=0;foreach ($items['items'] as $timestamp => $_items) {$i++;?>
	<?$i=0;foreach ($_items as $item) {$i++;?>
	<tr>
		<?if($i===1){?><td style="vertical-align: middle; text-align: right;" rowspan="<?=count($_items)?>"><p><?=date('d.m.Y', $timestamp)?></p><p class="description"><?=date('H.i.s', $timestamp)?></p></td><?}?>
		<td><?=$item['name']?></td>
		<td><?=$item['manufacture']?></td>
		<td><?=$item['pack']?></td>
		<?if($i===1){?><td style="vertical-align: middle; text-align: right;" rowspan="<?=count($_items)?>"><p><?=$item['phone']?></p><p class="description"><?=$item['readdress']?></p></td><?}?>
	</tr>
	<?}?>
<?}?>
</table>
<div class="delimiter-block"></div>
<p class="description">Количество звонков за период: <?=$items['total_rows']?></p>
<p class="description">Количество отмеченных предложений за период: <?=$items['total_prices']?></p>
<div class="attention-info" style="font-size: 0.85em;">
	<p>* Отчет "Статистика звонков" показывает сколько звонков было принято операторами справочной телефонной службы, 
	где в ответ на запрос абонента была выдана информация о Вашей фирме. Для каждого звонка указывается какие именно предложения (товары или
	услуги из прайс-листа) были выданы, а в случае проведения процедуры переадресации звонка показывается телефон Вашей фирмы, куда был переведен
	звонок и результат переадресации.</p>
</div>
<?}?>