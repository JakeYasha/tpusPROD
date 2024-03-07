<?if($items){?>
	<?foreach ($items as $item){?>
	<div class="review">
			<?= app()->chunk()->setArgs([$item['score'], true])->render('rating.stars')?>
		<div class="main_review">
			<div class="name_date">
				<span class="name"><?=$item['user']?></span>, <span class="date"><?=$item['date']?>:</span>
			</div>
			<p><?=strip_tags($item['text'])?></p>
		</div>
		<?if($item['reply_text']){?>
		<div class="main_review reply">
			<div class="name_date">
				<span class="name"><?=$item['reply_user_name']?></span>, <span class="date"><?=$item['reply_date']?>:</span>
			</div>
			<p><?=strip_tags($item['reply_text'])?></p>
		</div>
		<?}?>
	</div>
	<?}?>
<?}?>