<?if($items){?>
	<div class="search_adv_block">
		<span class="title">РЕКЛАМА</span>
			<div class="search_adv_block_container"><!--noindex-->
            <?  foreach ($items as $banner) {?><div class="adv_block">
            <?if($banner->hasImage()){$image = $banner->getImage();if($image->exists()){?><div class="image"><?=app()->adv()->renderBannerImageLink($banner, $image)?></div><?}?><?}?>
            <div class="adv-text<?=$banner->hasImage() ? '' : ' no-margin'?>">
				<div class="title"><a target="_blank" href="<?=$banner->link()?>" rel="nofollow"><?=$banner->val('header')?></a></div>
				<p><?=$banner->val('adv_text')?></p>
                <span><?=$banner->val('about_string')?></span>
				<?$advert_restrictions = $banner->getAdvertRestrictions(); if($advert_restrictions){?>
					<p class="adv-restrictions"><?=$advert_restrictions?></p>
				<?}?>
			</div>
			</div><?}?>
			<!--/noindex--></div>
	</div>
<?}?>