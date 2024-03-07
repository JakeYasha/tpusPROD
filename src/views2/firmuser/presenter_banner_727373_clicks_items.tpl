<?if($items){?>
<table class="default-table banner-table" style="width: 100%;">
	<tr>
		<th>Дата</th>
		<th>IP</th>
		<th>Со страницы</th>
		<th>URL перехода</th>
	</tr>
<?foreach ($items as $item) {?>
	<tr>
		<td style="text-align: right"><p><?=$item['date']?></p><p class="description"><?=$item['time']?></p></td>
		<td style="vertical-align: middle;"><?=$item['ip']?></td>
		<td style="vertical-align: middle;"><a target="_blank" href="<?=$item['page_url']?>"><?=  str()->crop($item['page_url'], 80, '-')?></a></td>
		<td style="vertical-align: middle;"><a target="_blank" href="<?=$item['url']?>"><?=  str()->crop($item['url'], 80, '-')?></a></td>
	</tr>
<?}?>
</table>
<?} else {?>
<div class="cat_description">
	<p>Нет данных</p>
</div>
<?}?>
