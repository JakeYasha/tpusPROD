<div class="cart-block">
    <h1>Оформление заказа</h1>
    <ul class="order-navigation">
        <li><a href="">Контактные данные</a></li>
        <li><a href="">Доставка и оплата</a></li>
        <li class="active"><a href="">Завершение заказа</a></li>
    </ul>
    <div class="order-step">
        <h2>Заказ №<?= $order->id() ?></h2>
        <div class="cart-block-firm">
            <span class="firm-name">Продавец: <span><a href="https://www.tovaryplus.ru<?=$firm->link()?>"><?=$firm->name()?></a></span></span>
            <table>
                <tr>
                    <th colspan="2">Наименование товара и описание</th>
                    <th class="w150">Количество</th>
                    <th class="w150">Цена</th>
                </tr>
                <tr>
                    <? foreach ($order_goods['items'] as $item) { ?>
                        <? if (isset($item['image'])) { ?>
                            <td><div class="image" style="position: relative;"><a href="https://www.tovaryplus.ru<?= $item['link'] ?>" target="_blank"><img src="https://www.tovaryplus.ru<?= $item['image'] ?>" style="max-width:200px;" width="200px" /></a></div></td>
                        <? } else { ?>
                            <td><div class="image no-image" style="position: relative;"><a href="https://www.tovaryplus.ru<?= $item['link'] ?>" target="_blank"></a></div></td>
                        <? } ?>
                        <td>
                            <div class="product-description">
                                <a href="https://www.tovaryplus.ru<?= $item['link'] ?>"><?= $item['name'] ?></a>
                                <p><?= $item['info'] ?><br/><? if ($item['production']) { ?> Производство: <?= $item['production'] ?><? if ($item['pack']) { ?>, фасовка: <?= $item['pack'] ?><? } ?><? } else { ?><? if ($item['pack']) { ?>Фасовка: <?= $item['pack'] ?><? } ?><? } ?></p>
                            </div>
                        </td>
                        <td>
                            <span><?= $item['count'] ?><span>шт.</span></span>
                        </td>
                        <td>
                            <? if ($item['price']) { ?><span class="product-price new-price"><?= $item['price'] ?><span class="price-parameter">шт.</span></span><? } ?>
                            <? if ($item['old_price']) { ?><span class="product-price old-price"><?= $item['old_price'] ?><span class="price-parameter">шт.</span></span><? } ?>
                            <!--div class="discount"><span>-25%</span></div-->
                        </td>
                    </tr>
                <? } ?>
            </table>
            <div class="total-cost">
                <span>Общая стоимость: <span><?= $order_goods['cost'] ?> руб.</span></span>
            </div>
        </div>
        <div class="totals">
            <div class="personal-info">
                <h3>Контактные данные</h3>
                <span><span>ФИО: </span><?= $order_user->val('name') ?></span>
                <span><span>Тел.: </span><?= $order_user->val('phone') ?></span>
                <span><span>E-mail: </span><?= $order_user->val('email') ?></span>
            </div>
            <div class="delivery-info">
                <span class="h3">Получение товара: <span><?= $delivery ?></span></span>
                <span class="h3">Оплата: <span><?= $payment ?></span></span>
            </div>
        </div>
        <a href="<?=$is_cart_empty ? '/catalog/' : '/cart/' ?>" class="btn back-to-site">Вернуться на сайт</a>
    </div>
</div>