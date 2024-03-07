<?if($items){?>
        <div class="header_adv_block"><!--noindex-->
            <?  foreach ($items as $banner) {?>
                <?if($banner->hasImage()){$image = $banner->getImage();?><?=app()->adv()->renderBannerImageLink($banner, $image)?><?}?>
            <?}?>
        <!--/noindex--></div>
<?}?>