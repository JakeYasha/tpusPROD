<div class="mdc-layout-grid">
    <div class="mdc-layout-grid__inner">
        <div class="mdc-layout-grid__cell--span-12">
            <h1 class="page-title page-title_cart">Оформление заказа </h1>
            <div class="steps">
                <div class="steps__item"><span>1</span>Контактные данные</div>
                <a class="steps__item"><span>2</span>Доставка и оплата</a>
                <a class="steps__item steps__item_active"><span>3</span>Завершение заказа</a>
            </div>
            <span class="order-number">Заказ №<?= $order->id() ?></span>
            <div class="cart">
                <div class="cart__seller">Продавец: <span><a href="https://www.tovaryplus.ru<?=$firm->link()?>"><?=$firm->name()?></a></span></div>
                <table class="cart__table">
                    <thead>
                        <tr>
                            <th colspan="2">Наименование товара и описание</th>
                            <th>Количество</th>
                            <th>Цена</th>
                        </tr>
                    </thead>
                    <tbody>
                        <? foreach ($order_goods['items'] as $item) { ?>
                            <tr class="cart__item">
                                <? if (isset($item['image'])) { ?>
                                    <td>
                                        <div class="cart__item-image">
                                            <a href="https://www.tovaryplus.ru<?= $item['link'] ?>" target="_blank"><img src="https://www.tovaryplus.ru<?= $item['image'] ?>" alt="" class="img-fluid"></a>
                                        </div>
                                    </td>
                                <? } else { ?>
                                    <td>
                                        <div class="cart__item-image no-image">
                                            <a href="https://www.tovaryplus.ru<?= $item['link'] ?>" target="_blank"></a>
                                        </div>
                                    </td>
                                <? } ?>
                                <td>
                                    <div class="cart__item-description">
                                        <a class="cart__item-link" href="https://www.tovaryplus.ru<?= $item['link'] ?>"><?= $item['name'] ?></a>
                                        <p><?= $item['info'] ?><br/><? if ($item['production']) { ?> Производство: <?= $item['production'] ?><? if ($item['pack']) { ?>, фасовка: <?= $item['pack'] ?><? } ?><? } else { ?><? if ($item['pack']) { ?>Фасовка: <?= $item['pack'] ?><? } ?><? } ?></p>
                                    </div>
                                </td>
                                <td>
                                    <div class="cart__item-count">
                                        <input type="text" class="form__control" value="<?= $item['count'] ?>" disabled>
                                        <span>шт.</span>
                                    </div>
                                </td>
                                <td class="cart__item-price">
                                    <? if ($item['price']) { ?><?= $item['price'] ?> р/шт<? } ?>
                                    <? if ($item['old_price']) { ?><?= $item['old_price'] ?> р/шт<? } ?>
                                    <!--div class="discount"><span>-25%</span></div-->
                                </td>
                            </tr>
                        <?}?>
                    </tbody>
                </table>
                <div class="cart__total">Общая стоимость: <span><?= $order_goods['cost'] ?> руб.</span></div>
            </div>
            
            <h2 style="margin-bottom: 1.5rem">Контактные данные</h2>
            <div class="order-contacts">ФИО: <span><?= $order_user->val('name') ?></span></div>
            <div class="order-contacts">Тел: <span><?= $order_user->val('phone') ?></span></div>
            <div class="order-contacts">E-mail: <span><?= $order_user->val('email') ?></span></div>
            <div class="order-contacts">Получение товара: <span><?= $delivery ?></span></div>
            <div class="order-contacts">Оплата: <span><?= $payment ?></span></div>
            <div style="text-align: center; margin-top: 2rem">
                <a href="<?=$is_cart_empty ? '/catalog/' : '/cart/' ?>" class="btn btn_primary">Вернуться на сайт</a>
            </div>
        </div>
    </div>
</div>