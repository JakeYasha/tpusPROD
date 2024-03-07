<div class="popup wide" style="width: 600px; height: 400px; background: transparent;">	
	<div class="top_field">
		<div class="title"><?=$item->val('name')?></div>
		<?if($item->isYoutube()) {?>
			<iframe style="height: 325px;margin-left: -45px;margin-top: 0;width: 600px; border: none;" src="https://www.youtube.com/embed/<?=$item->getYoutubeHash()?>" allowfullscreen></iframe>
		<?} else {?>
		<?=$item->val('video_code')?>
		<?}?>
	</div>	
</div>