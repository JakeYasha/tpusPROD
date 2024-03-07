<div class="item_info offset-none">
	<div class="search-result">
		<?= $tabs?>
		<?if($items){?>
		<h2><?=app()->metadata()->getTitle()?></h2>
		<div class="comp_video">
			<?foreach ($items as $k => $video) {?>
				<div class="video">
					<a class="sprite-video" href="<?=app()->linkFilter($firm->link(), ['mode' => 'video', 'id' => $video->id()])?>"></a>
					<div class="video_field">
						<a href="<?=app()->linkFilter($firm->link(), ['mode' => 'video', 'id' => $video->id()])?>"><img src="<?=$video->getThumbnailSrc()?>" height="150" width="200" alt="<?=str()->replace($video->name(), ['"'], ['&quot;'])?>"></a>
						<span><?=$video->val('video_length')?></span>
					</div>
					<div class="video_text">
						<p><a href="<?=app()->linkFilter($firm->link(), ['mode' => 'video', 'id' => $video->id()])?>"><?=$video->name()?></a></p>
					</div>
				</div>
			<?}?>
			</div>
		<?}?>
	</div>
</div>
<div class="pre_footer_adv_block">
<?=app()->chunk()->render('adv.bottom_banners')?>
</div>