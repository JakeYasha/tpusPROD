<?if($is_swf){?>
        <div class="js-flash-banner" data-url="<?=$advert_module->link()?>?fix=1"><embed wmode="transparent" src="<?=$image->embeddedFile()->link()?>?link_url=<?=$advert_module->link()?>" style="width: 200px;" /></div>
<?} else {?>
        <a target="_blank" href="<?=$advert_module->link()?>" rel="nofollow"><img<?if($image_style){?> style="<?=$image_style?>"<?}?> src="<?=$image->link()?>" /></a>
<?}?>
