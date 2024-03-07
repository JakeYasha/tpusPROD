
<?if($items){?>
<div class="delimiter-block"></div>
<div class="delimiter-block"></div>
<h2>Статистика баннера #<?=current($items)['id']?></h2>
<table>
	<tr>
		<th>Дата</th>
		<th>IP</th>
		<th>Со страницы</th>
	</tr>
<?foreach ($items as $item) {?>
	<tr>
		<td style="text-align: right"><p><?=$item['date']?></p><p class="description"><?=$item['time']?></p></td>
		<td style="vertical-align: middle;"><?=$item['ip']?></td>
		<td style="vertical-align: middle;"><a target="_blank" href="<?=$item['page_url']?>"><?=  str()->crop($item['page_url'],80,'-')?></a></td>
	</tr>
<?}?>
</table>
<?}?>