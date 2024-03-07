<?if($items){?>
<div class="search_result_content ">
	<table class="default-table price-table">
		<tr>
			<th style="width: 30px;">Тип</th>
			<th>Название</th>
			<th style="width: 170px;">Дата действия</th>
			<th style="width: 60px;">Просмотры</th>
			<th style="width: 60px;">&nbsp;</th>
		</tr>
		<?$i=0;foreach ($items as $item) {$i++;?>
		<tr<?if(!$item['is_active']){?> style="opacity: .5"<?}?>>
			<td>
				<?if($item['flag_is_present']){?>
					<div class="image" style="position: relative;"><span class="image promo-value present" title="Подарок"></span></div>
				<?} elseif((int)$item['percent_value'] !== 0) {?>
					<div class="image" style="position: relative;"><div class="promo-value" title="Скидка"><?=$item['percent_value']?>%</div></div>
				<?} else {?>
					<div class="image" style="position: relative;"><div class="promo-value" title="Акция">%</div></div>
				<?}?>
			</td>
			<td><a target="_blank" href="<?=$item['link']?>"><?=$item['name']?></a><p class="description"><?=  strip_tags($item['text'])?></p></td>
			<td style="vertical-align: middle;"><?=$item['time_beginning_short']?> &mdash; <?=$item['time_ending_short']?></td>
			<td style="vertical-align: middle;"><?=$item['total_views']?></td>
			<td style="vertical-align: middle; text-align: center;">
				<a title="Изменить" href="/firm-user/promo/?mode=edit&id=<?=$item['id']?>" class="edit-btn"></a>
				<a title="Удалить" onclick="return confirm('Подтвердите удаление...')" href="/firm-user/promo/?mode=delete&id=<?=$item['id']?>" class="delete-btn"></a>
			</td>
		</tr>
		<?}?>
	</table>
</div>
<?}?>