<?if($items){?>
        <div class="header_adv_block">
		<!--noindex-->
            <?  foreach ($items as $advert_module) {?>
                <?if($advert_module->hasImage()){$image = $advert_module->getImage();?><?=app()->adv()->renderAdvertModuleImageLink($advert_module, $image)?><?}?>
            <?}?>
		<!--/noindex-->	
        </div>
<?}?>