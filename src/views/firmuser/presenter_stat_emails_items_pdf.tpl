<?if($items){?>
<div class="delimiter-block"></div>
<div class="delimiter-block"></div>
<div class="delimiter-block"></div>
<h1>Статистика email, sms *</h1>
<table>
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
		<?if($i===1){?><td style="vertical-align: middle;" rowspan="<?=count($_items)?>"><p>email</p></td><?}?>
		<td style="text-align: right"><p><?=$item['date']?></p><p class="description" style="text-align: right;"><?=$item['time']?></p></td>
		<td><?=$item['name']?></td>
		<td><?=$item['pack']?></td>
		<td><?=$item['unit']?></td>
	</tr>
	<?}?>
<?}?>
</table>
<div class="delimiter-block"></div>
<p class="description">Количество экспортных сессий (отправленных писем): <?=$items['total_rows']?></p>
<p class="description">Количество отмеченных предложений: <?=$items['total_prices']?></p>
<div class="attention-info" style="font-size: 0.85em;">
	<p>* Отчет "Статистика email, sms" показывает, сколько запросов было принято операторами справочной телефонной службы 727373 Информационного центра "Товары плюс" на формирование для абонента ответа с отправкой на его email или sms, 
	где в ответ на запрос была выдана информация о Вашей фирме. Для каждого запроса указывается какие именно предложения (товары или услуги из прайс-листа) были отмечены в экспортной сессии.</p>
</div>
<?}?>