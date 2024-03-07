<div class="banner-wrapper"><?
        if ($is_swf) { 
            ?><div class="js-flash-banner" data-url="<?= $banner->link() ?>?fix=1"><embed wmode="transparent" src="<?= $image->embeddedFile()->link() ?>?link_url=<?= $banner->link() ?>" style="width: 200px;" /></div><?
        } else { 
            ?><a target="_blank" href="<?= $banner->link() ?>" rel="nofollow"> <img<? if ($image_style) { ?> style="<?= $image_style ?>"<? } ?> src="<?= $image->link() ?>" alt="Реклама на tovaryplus.ru" /> </a><?
        if ($banner->val('advertising_copy_text')) { 
            ?><a href="#" class="advertising-copy-text-link js-advertising-copy-text">О компании</a><div class="advertising-copy-text js-advertising-copy-text-div"><?= nl2br($banner->val('advertising_copy_text'), true) ?></div><?
        } ?><?
    } 
?></div>