
<div class="cart-block">
    <div class="order-step">
        <h2>Заказ №<?= $params['order_id'] ?> от <?= $params['order_date'] ?></h2>
        <div class="cart-block-firm">
            <span class="firm-name">Продавец: <span><a href="https://www.tovaryplus.ru<?=$params['firm_link']?>"><?= $params['firm_name'] ?></a></span></span>
            <table>
                <tr>
                    <th colspan="2">Наименование товара и описание</th>
                    <th class="w150">Количество</th>
                    <th class="w150">Цена</th>
                </tr>
                <tr>
                    <? foreach ($params['order_goods']['items'] as $item) { ?>
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
                            <? if ($item['price']) { ?><span class="product-price new-price"><?= $item['price'] ?><span class="price-parameter">руб.</span></span><? } ?>
                            <? if ($item['old_price']) { ?><span class="product-price old-price"><?= $item['old_price'] ?><span class="price-parameter">руб.</span></span><? } ?>
                            <!--div class="discount"><span>-25%</span></div-->
                        </td>
                    </tr>
                <? } ?>
            </table>
            <div class="total-cost">
                <span>Общая стоимость: <span><?= $params['order_goods']['cost'] ?> руб.</span></span>
            </div>
        </div>
        <div class="totals">
            <div class="personal-info">
                <h3>Контактные данные</h3>
                <span><span>ФИО: </span><?= $params['order_user']->val('name') ?></span>
                <span><span>Тел.: </span><?= $params['order_user']->val('phone') ?></span>
                <span><span>E-mail: </span><?= $params['order_user']->val('email') ?></span>
            </div>
            <div class="delivery-info">
                <span class="h3">Получение товара: <span><?= $params['delivery'] ?></span></span>
                <span class="h3">Оплата: <span><?= $params['payment'] ?></span></span>
            </div>
        </div>
    </div>
    <div>Для отмены заказа перейдите пожалуйста по <a href="//www.tovaryplus.ru/order/delete/?order=<?= $params['order_id'] ?>&user=<?= $params['order_user']->id() ?>">ссылке</a></div>
</div>