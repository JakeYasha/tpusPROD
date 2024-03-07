<?if($items){?>
	<?  foreach ($items as $banner) {$image = $banner->getImage();?>
		<a target="_blank" href="<?=$banner->link()?>" rel="nofollow"><img style="max-width: 300px;" src="<?=$image->val('file_extension') === 'swf' ? '/img/file_swf.png' : $image->link()?>" /></a>
		<div class="title"><a target="_blank" href="<?=$banner->link()?>" rel="nofollow"><?=$banner->val('header')?></a></div>
		<p><?=$banner->val('adv_text')?></p>
		<span><?=$banner->val('about_string')?></span>
	<?}?>
<?}?>