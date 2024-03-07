<?if($items){?>
    <div class="search_adv_block">
	<!--noindex-->
        <span class="title">РЕКЛАМА</span>
        <div class="search_adv_block_container">
            <?  foreach ($items as $advert_module) {?>
				<div class="adv_block">
					<?if($advert_module->hasImage()){$image = $advert_module->getImage();if($image->exists()){?><div class="image"><?=app()->adv()->renderAdvertModuleImageLink($advert_module, $image)?></div><?}?><?}?>
                    <div class="adv-text<?=$advert_module->hasImage() ? '' : ' no-margin'?>">
                    <div class="title"><a target="_blank" href="<?=$advert_module->link()?>" rel="nofollow"><?=$advert_module->val('header')?></a></div>
						<p><?=$advert_module->val('adv_text')?></p>
						<span><?=$advert_module->val('about_string')?></span>
                    </div>
				</div>
			<?}?>
		</div>
	<!--/noindex-->	
    </div>
<?}?>