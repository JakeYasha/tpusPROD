<?if($items){?>
        <div class="mdc-layout-grid__inner"><!--noindex-->
            <div class="mdc-layout-grid__cell--span-3-desktop banner-hidden-md">
                <?  foreach ($items as $banner) {?>
                    <?if($banner->_temp_type === 'rubrics_small_banner') {?>
                        <?if($banner->hasImage()){$image = $banner->getImage();?><?=app()->adv()->renderBannerImageLink($banner, $image)?><?}?>
                    <?}?>
                <?}?>
            </div>
            <div class="mdc-layout-grid__cell--span-9-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
                <div class="mdc-layout-grid__inner">
                    <?  foreach ($items as $banner) {?>
                        <?if($banner->_temp_type !== 'rubrics_small_banner') {?>
                            <?if($banner->hasImage()){$image = $banner->getImage();?>
                                <div class="mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone"  style="position:relative;">
                                    <?=app()->adv()->renderBannerImageLink($banner, $image)?>
                                </div>
                            <?}?>
                        <?}?>
                    <?}?>
                </div>
            </div>
        <!--/noindex--></div>
<?}?>