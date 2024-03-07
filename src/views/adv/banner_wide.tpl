<?if($items) {?>
<div class="banner_advertising small">
<? foreach ($items as $banner) {?>
	<?if($banner->hasImage()){$image = $banner->getImage();if($image->exists()){?><div class="image"><?=app()->adv()->renderBannerImageLink($banner, $image)?></div><?}?><?}?>
<?}?>
</div>
<?}?>