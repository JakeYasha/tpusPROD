<?if($items){?>
<div class="search_result_content ">
	<table class="default-table price-table">
		<tr>
			<th>Пользователь</th>
			<th style="width: 80px;">Оценка</th>
			<th>Отзыв</th>
			<th style="width: 60px;">&nbsp;</th>
		</tr>
		<?$i=0;foreach ($items as $item) {$i++;?>
		<tr<?if(!$item['is_active']){?> style="opacity: .5"<?}?>>
			<td><p style="font-weight: bold;"><?=$item['user_name']?></p><p class="description"><?=$item['user_email']?></p><p class="description"><?=$item['datetime']?></p></td>
			<td style="text-align: center; vertical-align: middle"><?= app()->chunk()->setArgs([$item['score'], true])->render('rating.stars')?></td>
			<td><a target="_blank" href="<?=$item['link']?>"><?=$item['name']?></a><p class="description"><?=  strip_tags($item['text'])?></p></td>
			<td style="vertical-align: middle; text-align: center;">
				<?if($item['reply_text']){?>
				<a title="Изменить" href="/firm-user/review/?mode=edit&id=<?=$item['id']?>" class="edit-btn"></a>
				<?} else {?>
				<a title="Ответить" href="/firm-user/review/?mode=edit&id=<?=$item['id']?>" class="reply-btn"></a>
				<?}?>
			</td>
		</tr>
		<?}?>
	</table>
</div>
<?}?>