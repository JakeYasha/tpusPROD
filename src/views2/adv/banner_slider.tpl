<?if($items){?>
<?$__i=0;$def_exists=false;foreach ($items as $banner) {$__i++;if($banner->_temp_type === 'default' || $banner->_temp_type === 'context'){$def_exists = true;$__i--;break;}}?>
<div class="rubrics-right banners js-rubrics-banners adv-slider" style="display: inline-block;">
	<? 
        $slider = '<div class="rubrics-right-slider"><div class="jcarousel-wrapper"><div class="jcarousel'.($__i > 1 ? ' js-jcarousel-auto':'').'"><ul>'; 
        $i=0;
        $render_slider = false;
        foreach ($items as $banner) {
                $i++;
                if($banner->_temp_type === 'default' || $banner->_temp_type === 'context'){$i--;$default_banner = $banner; break;}
                if($banner->hasImage()){
                        $image = $banner->getImage();
                        if($image->exists()){ $render_slider = true; $slider .= '<li>' . app()->adv()->renderBannerImageLink($banner, $image, ''). '</li>';} 
                }
        }
        $slider .= '</ul></div>';
        if($i > 1){
		$slider .= '<a href="#" class="jcarousel-control-prev"></a><a href="#" class="jcarousel-control-next"></a><p class="jcarousel-pagination"></p>';
        }
        $slider .= '</div></div>';
        ?>
        <?=$render_slider ? $slider : '' ?>
        <?if(isset($default_banner)){?>
        <div class="rubrics-right-banners">
                <?if($default_banner->_temp_type === 'default'){?>
                        <?if($default_banner->hasImage()){$image = $default_banner->getImage();if($image->exists()){?><?=app()->adv()->renderBannerImageLink($default_banner, $image)?><?}?><?}?>
                <?} else {?>
                <?if($default_banner->hasImage()){$image = $default_banner->getImage();if($image->exists()){?><div class="image"><?=app()->adv()->renderBannerImageLink($default_banner, $image)?></div><?}?><?}?>
                <div class="search_adv_block in-slider">
                        <div class="adv-text<?=$default_banner->hasImage() ? '' : ' no-margin'?>">
                                <div class="title"><a target="_blank" href="<?=$default_banner->link()?>" rel="nofollow"><?=$default_banner->val('header')?></a></div>
                                <p><?=$default_banner->val('adv_text')?></p>
                                <span><?=$default_banner->val('about_string')?></span>
                        </div>
                </div>
                <?}?>
        </div>
	<?}?>
</div>
<?}?>