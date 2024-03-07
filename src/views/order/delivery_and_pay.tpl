<div class="cart-block">
    <h1>Оформление заказа</h1>
    <ul class="order-navigation">
        <li><a href="">Контактные данные</a></li>
        <li class="active"><a href="">Доставка и оплата</a></li>
        <li><a href="">Завершение заказа</a></li>
    </ul>
    <div class="order-step">
        <h2>Доставка и оплата</h2>
        <form action="/order/" method="POST">
            <input type="hidden" name="user" value="<?= $user_hash ?>"/>
            <input type="hidden" name="order" value="<?= $order_id ?>"/>
            <input type="hidden" name="step" value="3"/>

            <h3>Получение товара:</h3>
            <div class="table-head">
                <span class="type-delivery">Способ доставки</span>
                <span class="price-delivery">Стоимость доставки</span>
                <span class="time-delivery">Срок доставки</span>
            </div>
            <ul class="delivery-payment-choose delivery-choose">
                <? if ($firm->hasDelivery()) { ?> 
                    <? foreach ($delivery['types'] as $k => $v) { ?>
                        <li>
                            <input id="type-delivery-<?= $k ?>" class="radio" name="delivery" type="radio" value="<?= $k ?>"/>
                            <label for="type-delivery-<?= $k ?>">
                                <span class="type-delivery"><?= $v ?></span>
                                <span class="price-delivery">Бесплатно</span>
                                <span class="time-delivery">ежедневно, 8-22</span>
                            </label>
                        </li>
                    <? } ?>
                <? } else { ?>
                    <li>
                        <input id="type-delivery-4" class="radio" name="delivery" type="radio" value="4"/>
                        <label for="type-delivery-4">
                            <span class="type-delivery">Самовывоз, <?= $firm->address() ?></span>
                            <span class="price-delivery">Бесплатно</span>
                            <span class="time-delivery">ежедневно, 8-22</span></label>
                    </li>
                <? } ?>
            </ul>
            <h3>Оплата</h3>
            <ul class="delivery-payment-choose">
                <? if ($payment) { ?> 
                    <? foreach ($payment as $k => $v) { ?>
                    <? } ?>
                <? } else { ?>
                    <li>
                        <input id="type-pay-1" class="radio" name="payment" type="radio" value="1"/>
                        <label for="type-pay-1"><span>Наличными при получении</span></label>
                    </li>
                <? } ?>
            </ul>
            <div class="buttons-wrapper">
                <button class="btn">Завершить</button>
            </div>
        </form>
    </div>
</div>