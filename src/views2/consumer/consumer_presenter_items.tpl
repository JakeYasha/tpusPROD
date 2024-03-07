<? if ($items) {?>
	<? $i = 0;
	foreach ($items as $item) {
		$i++;?>
		<div class="search_result_cell no_span"><span class="number"><?= $i?></span>
			<div class="title"><a href="<?=$item['link']?>">Вопрос от <?=date("d.m.Y", CDateTime::toTimestamp($item['timestamp_inserting']))?>, <?=$item['user_name']?></a></div>
				<div class="consumer_metadata"><?=$item['metadata_title']?></div>
				<div class="description"><br/>
					<?=$item['question']?>
				</div>
			<div class="description notice-blue">
				<?=  str()->crop($item['answer'], 300, '.', ' <a href="'.$item['link'].'">читать ответ полностью</a>')?>
			</div>
		</div>
	<? }?>
<?}?>