<?if($items){?>
<div class="adv_side js-fix-scroll">
	<?  foreach ($items as $banner) {?>
	<?if($banner->hasImage()){$image = $banner->getImage();?><?=app()->adv()->renderBannerImageLink($banner, $image)?><?}?>
	<?}?>
</div>
<?}?>