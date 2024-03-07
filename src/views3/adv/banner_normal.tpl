<?if($items){?>
        <div class="module">
            <div class="mdc-layout-grid__inner">
                <!--noindex-->
                <?  foreach ($items as $banner) {?>
                    <?if($banner->hasImage()){$image = $banner->getImage();?>
                        <div class="mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-4-tablet mdc-layout-grid__cell--span-4-phone" style="position:relative;">
                            <?=app()->adv()->renderBannerImageLink($banner, $image)?>
                        </div>
                    <?}?>
                <?}?>
                <!--/noindex-->
            </div>
			<div class="divider"></div>
        </div>
<?}?>