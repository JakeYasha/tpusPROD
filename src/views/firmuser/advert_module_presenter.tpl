<?if($items){?>
<div class="search_result_content ">
	<table class="default-table price-table">
		<tr>
			<th style="width: 30px;">Изображение</th>
			<th>Содержание модуля</th>
			<th style="width: 170px;">Дата действия</th>
                        <?if (app()->firmManager()->exists()){?>
                                <th style="width: 60px;">&nbsp;</th>
                        <?}?>
		</tr>
		<?$i=0;foreach ($items as $item) {$i++;?>
		<tr <?if(!$item['is_active']){?> style="opacity: .5"<?}?> >
			<td><img src="<?=$item['image']?>"/></td>
                        <td>
                                <?if (app()->firmManager()->exists()){?>
                                        <a href="/firm-user/advert-module/?mode=edit&id=<?=$item['id']?>"><?=$item['header']?></a>
                                <?} else {?>
                                        <strong><?=$item['header']?></strong>
                                <?}?>
                                <p class="description"><?=  strip_tags($item['text'])?></p>
                        </td>
			<td style="vertical-align: middle;"><?=$item['time_beginning_short']?> &mdash; <?=$item['time_ending_short']?></td>
                        <?if (app()->firmManager()->exists()){?>
                                <td style="vertical-align: middle; text-align: center;">
                                        <a title="Изменить" href="/firm-user/advert-module/?mode=edit&id=<?=$item['id']?>" class="edit-btn"></a>
                                        <a title="Удалить" onclick="return confirm('Подтвердите удаление...')" href="/firm-user/advert-module/?mode=delete&id=<?=$item['id']?>" class="delete-btn"></a>
                                </td>
                        <?}?>
		</tr>
		<?}?>
	</table>
</div>
<?}?>