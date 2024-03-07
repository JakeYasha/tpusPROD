<?=$bread_crumbs?>
<div class="cat_description">
	<?=$text?>
</div>
<div class="item_info">
	<div class="search_result">
		<?if($items){?>
		<div class="comp_video">
			<?foreach ($items as $k => $video) {$firm = new \App\Model\Firm();$firm->getByIdFirm($video->id_firm());?>
				<div class="video">
					<a class="sprite-video" href="<?=app()->linkFilter($firm->link(), ['mode' => 'video', 'id' => $video->id()])?>"></a>
					<div class="video_field">
						<a href="<?=app()->linkFilter($firm->link(), ['mode' => 'video', 'id' => $video->id()])?>"><img src="<?=$video->getThumbnailSrc()?>" height="150" width="200" alt="<?=str()->replace($video->name(), ['"'], ['&quot;'])?>"></a>
						<span><?=$video->val('video_length')?></span>
					</div>
					<div class="video_text">
						<p><a href="<?=app()->linkFilter($firm->link(), ['mode' => 'video', 'id' => $video->id()])?>"><?=$video->name()?></a></p>
					</div>
					<div class="org">
						<p><a href="<?=app()->linkFilter($firm->link(), ['mode' => 'video'])?>"><?=$firm->name()?></a></p>
					</div>
				</div>
			<?}?>
			</div>
		<?}?>
	</div>
</div>