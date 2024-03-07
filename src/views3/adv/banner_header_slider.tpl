<?if($items){?>
    <!--noindex-->
        <?  foreach ($items as $banner) {?>
            <?if($banner->hasImage()){$image = $banner->getImage();?>
                <?=app()->adv()->renderBannerImageLink($banner, $image)?>
            <?}?>
        <?}?>
    <!--/noindex-->
<?}?>