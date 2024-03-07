<?if($items){?>
<div class="search_result_content ">
	<table class="default-table price-table">
		<tr>
			<th style="width: 60px;">Дата</th>
			<th style="width: 250px;">Пользователь</th>
			<th>Сообщение</th>
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
			<td><a href="#" class="js-action js-firm-feedback-toggler"><?=$item['brief_text']?></a><p class="description hidden js-firm-feedback-string"><?=$item['flag_is_callback'] ? ('Заказ звонка со страницы: <a href="'.$item['text'].'" target="_blank">'.$item['text'].'</a>') : strip_tags(nl2br($item['text']))?></p></td>
			<td style="vertical-align: middle; text-align: center;">
				<?/*<a title="Ответить пользователю" href="/firm-user/feedback/?mode=send&id=<?=$item['id']?>" class="reply-btn"></a>*/?>
				<a title="Удалить" href="/firm-user/feedback/?mode=delete&id=<?=$item['id']?>" class="delete-btn"></a>
			</td>
		</tr>
		<?}?>
	</table>
</div>
<?}?>