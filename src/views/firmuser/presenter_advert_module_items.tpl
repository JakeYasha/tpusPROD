<?if($items){?>
<table class="default-table banner-table" style="width: 100%;">
	<tr>
		<th>Рекламные модули</th>
		<th style="width: 40%;">Рубрики</th>
		<th style="max-width: 100px;">Период размещения</th>
	</tr>
<?foreach ($items as $item) {?>
	<tr>
		<td><?=$item['block']?></td>
		<td class="banner-table-subgroup"><?if(is_array($item['subgroups'])){foreach($item['subgroups'] as $cat){?><a target="_blank" href="<?=app()->link($cat->link())?>"><?=$cat->name()?></a><?}}?></td>
		<td><?=$item['period']?></td>
	</tr>
<?}?>
</table>
<?=app()->chunk()->render('firmuser.call_support_block')?>
<?} else {?>
<div class="cat_description">
	<p>Нет данных</p>
</div>

<?}?>
