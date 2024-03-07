<?if($items){?>
<div class="search_result_content ">
	<table class="default-table price-table">
		<tr>
			<th>Название</th>
			<th style="width: 60px;">Просмотры</th>
			<th style="width: 60px;">&nbsp;</th>
		</tr>
		<?$i=0;foreach ($items as $item) {$i++;?>
		<tr<?if(!$item['is_active']){?> style="opacity: .5"<?}?>>
			<td><a target="_blank" href="<?=$item['link']?>"><?=$item['name']?></a><p class="description"><?=  strip_tags($item['text'])?></p></td>
			<td style="vertical-align: middle;"><?=$item['total_views']?></td>
			<td style="vertical-align: middle; text-align: center;">
				<a title="Изменить" href="/firm-user/video/?mode=edit&id=<?=$item['id']?>" class="edit-btn"></a>
				<a title="Удалить" onclick="return confirm('Подтвердите удаление...')" href="/firm-user/video/?mode=delete&id=<?=$item['id']?>" class="delete-btn"></a>
			</td>
		</tr>
		<?}?>
	</table>
</div>
<?}?>