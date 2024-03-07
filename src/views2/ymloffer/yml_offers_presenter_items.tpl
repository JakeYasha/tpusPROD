<?if($items){?>
<table class="default-table banner-table" style="width: 100%;">
	<tr>
		<th style="width: 33px;text-align: left;">Акт.</th>
		<th>Название</th>
	</tr>
<?foreach ($items as $item) {?>
	<tr>
		<td><input type="checkbox" class="js-yml-fix-offer-active" data-id-category="<?=$item['id']?>" /></td>
		<td><?=$item['name']?></td>
	</tr>
<?}?>
</table>
<?} else {?>
<div class="cat_description">
	<p>Нет данных</p>
</div>
<?}?>
