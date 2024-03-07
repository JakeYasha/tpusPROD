<?if($items){?>
<table class="default-table pages-table">
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
<?=$pagination ?? ''?>
<div class="delimiter-block"></div>
<p class="description">Количество звонков за период: <?=$items['total_rows']?></p>
<p class="description">Количество отмеченных предложений за период: <?=$items['total_prices']?></p>
<?} else {?>
<div class="cat_description">
	<p>Нет данных</p>
</div>
<?}?>
<div class="attention-info">
	<p>Отчет "Статистика звонков" показывает сколько звонков было принято операторами справочной телефонной службы 72-73-73 Информационного центра "Товары плюс", 
	где в ответ на запрос абонента была выдана информация о Вашей фирме. Для каждого звонка указывается какие именно предложения (товары или
	услуги из прайс-листа) были выданы, а в случае проведения процедуры переадресации звонка показывается телефон Вашей фирмы, куда был переведен
	звонок и результат переадресации.</p>
</div>
<?=app()->chunk()->render('firmuser.call_support_block')?>