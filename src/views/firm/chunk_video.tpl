<?foreach ($items as $k => $video) {?>
	<div class="video">
		<a class="sprite-video" href="<?=$video->link()?>"></a>
		<div class="video_field">
			<a href="<?=$video->link()?>"><img src="<?=$video->getThumbnailSrc()?>" height="150" width="200" alt="<?=str()->replace($video->name(), ['"'], ['&quot;'])?>"></a>
			<span><?=$video->val('video_length')?></span>
		</div>
		<div class="video_text">
			<p><a href="<?=$video->link()?>"><?=$video->name()?></a></p>
		</div>
	</div>
<?}?>