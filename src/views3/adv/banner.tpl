<a class="banner" target="_blank" href="<?= $banner->link() ?>" rel="nofollow">
    <img<? if ($image_style) { ?> style="<?= $image_style ?>"<? } ?> class="img-fluid" src="<?= $image->link() ?>" alt="Реклама на tovaryplus.ru" />
</a>
<style>
    .banner-text-a{
        position: absolute;
        bottom: 8px;
        background: #fffc;
        width: 100%;
        font-size: 14px;
        color: #000000ba;
        left: 0;
    }
    .banner-text-a>a{
        color: #000000ba!important;
        text-decoration: none!important;
        decoration: none!important;
    }
</style>
<?if ($banner->val('advertising_copy_text')) { ?>
    <span class="banner-text-a" style="font-size: 10px;">Реклама: <?echo($banner->val('advertising_copy_text'))?></span>
<? } ?>
