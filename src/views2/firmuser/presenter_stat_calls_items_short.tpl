<?if($items){?>
<table class="default-table pages-table">
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
                            <p class="description">
                                <audio controls=""><source src="/app-ajax/get-audio/?date_begin=<?=date('Y-m-d', $timestamp)?>&amp;date_end=<?=date('Y-m-d', $timestamp)?>&amp;asterisk_id=<?=$item['asterisk_id']?>" type="audio/mpeg"></audio>
                            </p>
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
<?=$pagination ?? ''?>
<div class="delimiter-block"></div>
<p class="description">Количество звонков за период: <?=$items['total_rows']?></p>
<p class="description">Количество отмеченных предложений за период: <?=$items['total_prices']?></p>
<?} else {?>
<div class="cat_description">
	<p>Нет данных</p>
</div>
<?}?>
