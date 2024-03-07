<?if($items){?>
<table class="default-table pages-table">
	<tr>
		<th>Тип</th>
		<th>Дата</th>
		<th>Наименование</th>
		<th>Ед. изм.</th>
		<th>Тип товара/услуги</th>
	</tr>
<?foreach ($items['items'] as $k=>$_items) {?>
	<?$i=0;foreach ($_items as $item) {$i++;?>
	<tr>
		<?if($i===1){?><td style="vertical-align: middle;" rowspan="<?=count($_items)?>"><p><?=$item['id_export_type']?></p></td><?}?>
		<td style="text-align: right"><p><?=$item['date']?></p><p class="description" style="text-align: right;"><?=$item['time']?></p></td>
		<td><?=$item['name']?></td>
		<td><?=$item['pack']?></td>
		<td><?=$item['unit']?></td>
	</tr>
	<?}?>
<?}?>
</table>
<?=$pagination ?? ''?>
<div class="delimiter-block"></div>
<p class="description">Количество экспортных сессий (отправленных писем): <?=$items['total_rows']?></p>
<p class="description">Количество отмеченных предложений: <?=$items['total_prices']?></p>
<?} else {?>
<div class="cat_description">
	<p>Нет данных</p>
</div>
<?}?>
