<? if ($item['is_yml']) { ?>
    <a href="#" onclick="return false;" class="btn-base btn-grey w130 disabled">В корзину</a>
<? } else { ?>
    <a class="btn-base btn-grey buy w130 fancybox fancybox.ajax" href="<?= app()->link('/app-ajax/add-to-cart/?price_id=' . $item['id'] . '&count=1')?>" rel="nofollow">В корзину</a>
<? } ?>