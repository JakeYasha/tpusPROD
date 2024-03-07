<?if($items){?>
<div class="comp_video">
	<?foreach ($items as $k => $video) {$firm = new \App\Model\Firm();$firm->getByIdFirm($video->id_firm());?>
		<div class="video">
			<a rel="nofollow" class="sprite-video" href="<?=app()->linkFilter($firm->link(), ['mode' => 'video', 'id' => $video->id()])?>"></a>
			<div class="video_field">
				<a rel="nofollow" href="<?=app()->linkFilter($firm->link(), ['mode' => 'video', 'id' => $video->id()])?>"><img src="<?=$video->getThumbnailSrc()?>" height="150" width="200" alt="<?=str()->replace($video->name(), ['"'], ['&quot;'])?>"></a>
				<span><?=$video->val('video_length')?></span>
			</div>
			<div class="video_text">
				<p><a rel="nofollow" href="<?=app()->linkFilter($firm->link(), ['mode' => 'video', 'id' => $video->id()])?>"><?=$video->name()?></a></p>
			</div>
			<div class="org">
				<p><a href="<?=app()->linkFilter($firm->link())?>"><?=$firm->name()?></a></p>
			</div>
		</div>
	<?}?>
	</div>
<?}?>