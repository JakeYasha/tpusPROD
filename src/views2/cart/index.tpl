<div class="cart-block">
    <h1>Ваша корзина</h1><span class="prod-num-top">(товаров: <?= $items_count ?>)</span>
    <? foreach ($firm_items as $firm_item) { ?>
        <div class="cart-block-firm">
            <form action="/order/create/" method="POST">
                <input type="hidden" name="cart_hash" value="<?= $cart_hash ?>"/>
                <input type="hidden" name="firm_id" value="<?= $firm_item['firm']->id() ?>"/>
                <input type="hidden" name="step" value="1"/>

                <span class="firm-name">Продавец:<span><?= $firm_item['firm']->name() ?></span></span>
                <table>
                    <tr>
                        <th colspan="2">Наименование товара и описание</th>
                        <th class="w150">Количество</th>
                        <th class="w130">Цена</th>
                        <th class="w40"></th>
                    </tr>
                    <tr>
                        <? foreach ($firm_item['items'] as $item) { ?>
                            <? if ($item['image']) { ?>
                                <td><div class="image" style="position: relative;"><a href="<?= $item['link'] ?>" target="_blank"><img src="<?= $item['image'] ?>" /></a></div></td>
                            <? } else { ?>
                                <td><div class="image no-image" style="position: relative;"><a href="<?= $item['link'] ?>" target="_blank"></a></div></td>
                            <? } ?>
                            <td>
                                <div class="product-description">
                                    <a href="<?= $item['link'] ?>"><?= $item['name'] ?></a>
                                    <p><?= $item['info'] ?><br/><? if ($item['production']) { ?> Производство: <?= $item['production'] ?><? if ($item['pack']) { ?>, фасовка: <?= $item['pack'] ?><? } ?><? } else { ?><? if ($item['pack']) { ?>Фасовка: <?= $item['pack'] ?><? } ?><? } ?></p>
                                </div>
                            </td>
                            <td>
                                <input class="product-count" type="text" value="<?= $item['count'] ?>" data-update="/app-ajax/update-cart-good-count/?price_id=<?= $item['id'] ?>" data-count="<?= $item['count'] ?>" /> <? if ($item['unit']) { ?><?= $item['unit'] ?><? } ?>
                            </td>
                            <td>
                                <? if ($item['price']) { ?><span class="product-price new-price"><?= $item['price'] ?> <?= $item['currency'] ?><span class="price-parameter"><? if ($item['unit']) { ?><?= $item['unit'] ?><? } ?></span></span><? } ?>
                                <? if ($item['old_price']) { ?><span class="product-price old-price"><?= $item['old_price'] ?> <?= $item['currency'] ?><span class="price-parameter"><? if ($item['unit']) { ?><?= $item['unit'] ?><? } ?></span></span><? } ?>
                                <!--div class="discount"><span>-25%</span></div-->
                            </td>
                            <td class="w40">
                                <a class="del fancybox fancybox.ajax" href="/app-ajax/delete-from-cart/?price_id=<?= $item['id'] ?>"></a>
                            </td>
                        </tr>
                    <? } ?>
                </table>
                <div class="total-cost">
                    <span>Общая стоимость:<span><?= $firm_item['cost'] ?> руб.</span></span>
                    <div class="buttons-wrapper">
                        <button class="btn order-button" firm-id="<?= $firm_item['firm']->id() ?>" cart-hash="<?= $cart_hash ?>">Заказать</button>
                    </div>
                </div>
            </form>
        </div>
    <? } ?>
</div>