<?if($items){$items = $items['rubrics_big_banner'];?>
<?$__i=0;foreach ($items as $k=>$banner) {$__i++;if($banner->_temp_type === 'rubrics_small_banner'){$__i--;}}?>
<div class="rubrics-right banners js-rubrics-banners" style="display: inline-block;">
	<div class="rubrics-right-slider">
		<div class="jcarousel-wrapper" style="width: 100%;">
			<div class="jcarousel<?if($__i>1){?> js-jcarousel-auto<?}?>">
				<ul>
					<?$i=0;foreach ($items as $k=>$banner) {$i++;if($banner->_temp_type === 'rubrics_small_banner'){$i--;break;}?>
					<?if($banner->hasImage()){$image = $banner->getImage();if($image->exists()){?><li><?=app()->adv()->renderBannerImageLink($banner, $image)?><?}?></li><?}?>
					<?unset($items[$k]);}?>
				</ul>
			</div>
			<?if($__i>1){?>
			<a href="#" class="jcarousel-control-prev"></a>
			<a href="#" class="jcarousel-control-next"></a>
			<p class="jcarousel-pagination"></p>
			<?}?>
		</div>
	</div>
		<?if($items){?><div class="rubrics-right-banners" style="width: 296px;">
		<?$i=0;foreach ($items as $k=>$banner) {$i++;?>
		<?if($banner->hasImage()){$image = $banner->getImage();?><?=app()->adv()->renderBannerImageLink($banner, $image, $i===1 ? 'width: 297px; height: 170px;' : null)?><?}?>
		<?}?>
	</div><?}?>
</div>
<?}?>