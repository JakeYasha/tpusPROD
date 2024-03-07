<div class="basket-preview-wrapper">
    <div class="basket-preview">
        <div class="basket-num"><? if ($cart_good_count) { ?><span><?= $cart_good_count ?></span><? } ?></div>
        <span class="basket-span">Корзина</span>
        <? if ($cart_good_count) { ?>
            <div class="basket-preview-info" <!--style="display: none;"-->>
                 <table>
                         <? foreach ($items as $item) { ?>
                        <tr>
                            <td>
                                <? if ($item['image']) { ?>
                                    <div class="image" style="position: relative;"><a href="<?= $item['link'] ?>" target="_blank"><img style="max-width: 80px;" src="<?= $item['image'] ?>" /></a></div>
                                <? } else { ?>
                                    <div class="image no-image" style="position: relative;"><a href="<?= $item['link'] ?>" target="_blank"></a></div>
                                <? } ?>
                            </td>
                            <td><a href="<?= $item['link'] ?>"><?= $item['name'] ?></a></td>
                            <td><span class=""><?= $item['price'] ?> <?= $item['currency'] ?><?if($item['unit']){?> / <?= $item['unit']?><?}?></span></span></td>
                            <td><span class="sum"><?= $item['count'] ?> шт</span></td>
                            <td><a class="del fancybox fancybox.ajax" href="/app-ajax/delete-from-cart/?price_id=<?= $item['id'] ?>"></a></td>
                        </tr>
                    <? } ?>
                    <tr>
                        <td></td>
                        <td>Итого</td>
                        <td colspan="3"><span class="price-of-all"><?= $cart_good_cost ?> Р</span></td>
                    </tr>
                </table>
                <a rel="nofollow" href="<?= app()->link('/cart/')?>" class="to-order">Оформить заказ</a>
            </div>
        <? } ?>
    </div>
</div>