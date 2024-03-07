<div class="mdc-layout-grid">
    <div class="mdc-layout-grid__inner">
        <div class="mdc-layout-grid__cell--span-12">
            <h1 class="page-title page-title_cart">Оформление заказа</h1>
            <div class="steps">
                <div class="steps__item"><span>1</span>Контактные данные</div>
                <a class="steps__item steps__item_active"><span>2</span>Доставка и оплата</a>
                <a class="steps__item"><span>3</span>Завершение заказа</a>
            </div>
            <div class="order-step">
                <div class="mdc-layout-grid__inner">
                    <div class="mdc-layout-grid__cell--span-12">
                        <h2 style="margin-bottom: 1.5rem">Доставка и оплата</h2>
                        <form action="/order/?kcenter=1" method="POST">
                            <input type="hidden" name="user" value="<?= $user_hash ?>"/>
                            <input type="hidden" name="order" value="<?= $order_id ?>"/>
                            <input type="hidden" name="step" value="3"/>

                            <h3 style="margin-bottom: 1.5rem">Получение товара</h3>
                            <ul class="delivery-head">
                                <li class="delivery-head__item">Способ доставки</li>
                                <li class="delivery-head__item">Стоимость доставки</li>
                                <li class="delivery-head__item">Срок доставки</li>
                            </ul>
                            <? if ($firm->hasDelivery()) { ?> 
                                <? foreach ($delivery['types'] as $k => $v) { ?>
                                    <label>
                                        <input id="type-delivery-<?= $k ?>" type="radio" name="delivery" class="deliver-input-real" value="<?= $k ?>">
                                        <ul class="delivery-input__fake delivery-info">
                                            <li class="delivery-info__item"><?= $v ?></li>
                                            <li class="delivery-info__item">Бесплатно</li>
                                            <li class="delivery-info__item">ежедневно, 8-22</li>
                                        </ul>
                                    </label>
                                <? } ?>
                            <? } else { ?>
                                <label>
                                    <input id="type-delivery-4" type="radio" name="delivery" class="deliver-input-real" value="4">
                                    <ul class="delivery-input__fake delivery-info">
                                        <li class="delivery-info__item">Самовывоз, <?= $firm->address() ?></li>
                                        <li class="delivery-info__item">Бесплатно</li>
                                        <li class="delivery-info__item">ежедневно, 8-22</li>
                                    </ul>
                                </label>
                            <? } ?>
                            <h3 style="margin: 1.5rem 0">Оплата</h3>
                            <? if ($payment) { ?> 
                                <? foreach ($payment as $k => $v) { ?>
                                <? } ?>
                            <? } else { ?>
                                <label class="delivery-info">
                                    <input id="type-pay-1" type="radio" name="payment" class="deliver-input-real" value="1">
                                    <div class="delivery-input__fake">Наличными при получении</div>
                                </label>
                            <? } ?>
                            
                            <button class="btn btn_primary" style="margin-top: 1.5rem">Завершить</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>