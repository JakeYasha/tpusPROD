<div class="mdc-layout-grid">
    <div class="mdc-layout-grid__inner">
        <div class="mdc-layout-grid__cell--span-12">
            <h1 class="page-title page-title_cart">Ваша корзина <span>(товаров: <?= $items_count ?>)</span></h1>
            <? foreach ($firm_items as $firm_item) { ?>
                <form class="cart" action="/order/create/?kcenter=1" method="POST">
                    <input type="hidden" name="cart_hash" value="<?= $cart_hash ?>"/>
                    <input type="hidden" name="firm_id" value="<?= $firm_item['firm']->id() ?>"/>
                    <input type="hidden" name="step" value="1"/>

                    <div class="cart__seller">Продавец: <span><?= $firm_item['firm']->name() ?></span></div>
                    <table class="cart__table">
                        <thead>
                            <tr>
                                <th colspan="2">Наименование товара и описание</th>
                                <th>Количество</th>
                                <th>Цена</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <? foreach ($firm_item['items'] as $item) { ?>
                                <tr class="cart__item">
                                    <? if ($item['image']) { ?>
                                        <td><div class="cart__item-image"><a href="<?= $item['link'] ?>" target="_blank"><img alt="" class="img-fluid" src="<?= $item['image'] ?>" /></a></div></td>
                                    <? } else { ?>
                                        <td><div class="image no-image cart__item-image"><a href="<?= $item['link'] ?>" target="_blank"></a></div></td>
                                    <? } ?>
                                    <td>
                                        <div class="cart__item-description">
                                            <a href="<?= $item['link'] ?>" class="cart__item-link"><?= $item['name'] ?></a>
                                            <p><?= $item['info'] ?><br/><? if ($item['production']) { ?> Производство: <?= $item['production'] ?><? if ($item['pack']) { ?>, фасовка: <?= $item['pack'] ?><? } ?><? } else { ?><? if ($item['pack']) { ?>Фасовка: <?= $item['pack'] ?><? } ?><? } ?></p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="cart__item-count">
                                            <input type="text" class="cart__item-count-control form__control" value="<?= $item['count'] ?>" data-update="/app-ajax/update-cart-good-count/?price_id=<?= $item['id'] ?>" data-count="<?= $item['count'] ?>">
                                            <span><? if ($item['unit']) { ?><?= $item['unit'] ?><? } ?></span>
                                        </div>
                                    </td>
                                    <td class="cart__item-price">
                                        <? if ($item['price']) { ?><?= $item['price'] ?> <?= $item['currency'] ?> <? if ($item['unit']) { ?>/<?= $item['unit'] ?><? } ?><? } ?>
                                        <? if ($item['old_price']) { ?><?= $item['old_price'] ?> <?= $item['currency'] ?> <? if ($item['unit']) { ?>/<?= $item['unit'] ?><? } ?><? } ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <a class="btn btn_primary cart__item-btn btn_delete js-open-modal-ajax" href="#" data-target="messageForm" data-url="/app-ajax/delete-from-cart/?price_id=<?= $item['id'] ?>"><i class="material-icons">close</i></a>
                                    </td>
                                </tr>
                            <? } ?>
                        </tbody>
                    </table>
                    <div class="cart__total">Общая стоимость: <span><?= $firm_item['cost'] ?> руб.</span></div>
                    <div class="cart__btn">
                        <button type="submit" class="btn btn_primary" firm-id="<?= $firm_item['firm']->id() ?>" cart-hash="<?= $cart_hash ?>">Заказать</button>
                    </div>
                </form>
            <? } ?>
        </div>
    </div>
</div>