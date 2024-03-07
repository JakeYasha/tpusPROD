<?if($items){?>
<div class="search_result_content ">
	<table class="default-table price-table">
		<tr>
			<th style="width: 60px;">Дата</th>
			<th style="width: 250px;">Пользователь</th>
			<th>Заказ</th>
			<th style="width: 30px;">&nbsp;</th>
		</tr>
		<?$i=0;foreach ($items as $item) {$i++;?>
		<tr<?if(!$item['is_active']){?> style="opacity: .5"<?}?><?if(CDateTime::toTimestamp($item['timestamp_inserting']) > CDateTime::toTimestamp(app()->firmUser()->val('prev_logon_timestamp'))){?> class="new"<?}?>>
			<td><p><?=$item['date']?></p><p class="description" style="text-align: right;"><?=$item['time']?></p></td>
			<td>
				<p style="font-weight: bold;"><?=$item['user_name']?></p>
				<?if($item['user_phone']){?><p class="description"><?=$item['user_phone']?></p><?}?>
				<?if($item['user_email']){?><p class="description"><?=$item['user_email']?></p><?}?>
			</td>
			<td><a target="_blank" href="<?=$item['item_link']?>" title="<?=$item['item_name']?>"><?=$item['brief_text']?></a><p class="description"><?=strip_tags($item['text'])?></p></td></td>
			<td style="vertical-align: middle; text-align: center;">
				<?/*<a title="Ответить пользователю" href="/firm-user/request/?mode=send&id=<?=$item['id']?>" class="reply-btn"></a>*/?>
				<a title="Удалить" href="/firm-user/request/?mode=delete&id=<?=$item['id']?>" class="delete-btn"></a>
			</td>
		</tr>
		<?}?>
	</table>
</div>
<?}?>