<?if($items){?>
<div class="delimiter-block"></div>
<div class="delimiter-block"></div>
<div class="delimiter-block"></div>
<h1>Статистика звонков *</h1>
<table>
	<tr>
		<th>Дата/Диспетчер</th>
		<th>Наименование организации</th>
		<th>Товар/Услуга</th>
		<th>Переадресация</th>
	</tr>
    <?$i=0;$j=0;foreach ($items['items'] as $timestamp => $firms) {?>
        <? $count_items=0; foreach ($firms as $_f => $_i) { $count_items += count($_i); }?>
        <?$i=0;foreach ($firms as $firm => $_items) {?>
            <?$j=0;foreach ($_items as $item) {$i++;$j++;?>
                <tr>
                    <?if ($i === 1) {?>
                        <td style="vertical-align: middle; text-align: right;" rowspan="<?=$count_items?>">
                            <p><?=date('d.m.Y', $timestamp)?></p>
                            <p class="description"><?=date('H.i.s', $timestamp)?></p>
                            <p class="description"><?=$item['dispatcher']?></p>
                        </td>
                    <?}?>
                    <?if ($j === 1) {?>
                        <td style="vertical-align: middle; text-align: left;" rowspan="<?=count($_items)?>">
                            <p><?=$item['firm_name']?></p>
                        </td>
                    <?}?>
                    <td style="vertical-align: middle; text-align: left;"><?=$item['firm_name'] == $item['name'] ? '-' : $item['name']?></td>
                    <?if ($j === 1) {?>
                        <td style="vertical-align: middle; text-align: right;" rowspan="<?=count($_items)?>">
                            <p><?=$item['phone']?></p>
                            <p class="description"><?=$item['readdress']?></p>
                        </td>
                    <?}?>
                </tr>
            <?}?>
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