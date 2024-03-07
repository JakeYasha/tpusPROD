<div class="cart-block">
    <h1>Оформление заказа</h1>
    <ul class="order-navigation">
        <li class="active"><a href="">Контактные данные</a></li>
        <li><a href="">Доставка и оплата</a></li>
        <li><a href="">Завершение заказа</a></li>
    </ul>
    <div class="order-step">
        <h2>Контактные данные</h2>
        <form action="/order/" method="POST">
            <div class="form-block wide">
                <input type="hidden" name="user" value="<?=$user_hash?>"/>
                <input type="hidden" name="order" value="<?=$order_id?>"/>
                <input type="hidden" name="step" value="2"/>
                <label>
                    <span>ФИО</span>
                    <input type="text" name="name" value="<?=$name?>" />
                </label>
                <label>
                    <span>Телефон:</span>
                    <input type="text" name="phone" value="<?=$phone?>" />
                </label>
                <label>
                    <span>E-mail:</span>
                    <input type="text" name="email" value="<?=$email?>" />
                </label>
            </div>
            <div class="form-block narrow">
                <h3>Уже покупали у нас?</h3>
                <a href="">Войти на сайт</a>
            </div>
            <label class="subscribe">
                <input type="checkbox" name="subscribed" <?=$subscribed ? "checked" : ""?> />
                <span>Подписаться на рассылку скидок и специальных предложений</span>
            </label>
            <div class="buttons-wrapper">
                <button class="btn">Продолжить</button>
            </div>
        </form>
    </div>
    <!--div class="order-step">
        <h2>Доставка и оплата</h2>
        <form>
            <h3>Получение товара:</h3>
            <div class="table-head">
                <span class="type-delivery">Способ доставки</span>
                <span class="price-delivery">Стоимость доставки</span>
                <span class="time-delivery">Срок доставки</span>
            </div>
            <ul class="delivery-payment-choose delivery-choose">
                <li>
                    <input id="radio1" class="radio" name="radio" type="radio">
                    <label for="radio1"><span class="type-delivery">Самовывоз. Адрес: г.Ярославль, ул.Матросова, 9</span><span class="price-delivery">Бесплатно</span><span class="time-delivery">ежедневно, 8-22</span></label>
                </li>
                <li>
                    <input id="radio2" class="radio" name="radio" type="radio">
                    <label for="radio2"><span class="type-delivery">Транспортная компания. Адрес пункта выдачи.</span><span class="price-delivery">1000 руб.</span><span class="time-delivery">3-5 рабочих дней.</span></label>
                </li>
                <li>
                    <input id="radio3" class="radio" name="radio" type="radio">
                    <label for="radio3"><span class="type-delivery">Какой то текст.</span><span class="price-delivery">1000 руб.</span><span class="time-delivery">3-5 рабочих дней.</span></label>
                </li>
            </ul>
            <h3>Оплата</h3>
            <ul class="delivery-payment-choose">
                <li>
                    <input id="radio2_1" class="radio" name="radio2" type="radio">
                    <label for="radio2_1"><span>Наличными при получении</span></label>
                </li>
                <li>
                    <input id="radio2_2" class="radio" name="radio2" type="radio">
                    <label for="radio2_2"><span>Другой способ оплаты</span></label>
                </li>
                <li>
                    <input id="radio2_3" class="radio" name="radio2" type="radio">
                    <label for="radio2_3"><span>Другой способ оплаты</span></label>
                </li>
            </ul>
            <div class="buttons-wrapper">
                <button class="btn">Завершить</button>
            </div>
        </form>
    </div>
    <div class="order-step">
        <h2>Заказ №10</h2>
        <div class="cart-block-firm">
            <span class="firm-name">Продавец:<span>ООО "Рога и Копыта"</span></span>
            <table>
                <tr>
                    <th colspan="2">Наименование товара и описание</th>
                    <th class="w150">Количество</th>
                    <th class="w150">Цена</th>
                </tr>
                <tr>
                    <td><a href=""><img src="http://tp43.sky-cms.ru/public/css/img/tapki1.jpg" /></a></td>
                    <td>
                        <div class="product-description">
                            <a href="">Название товара</a>
                            <p>Женские тапочки-балетки. Верх выполнен из 100% хлопкового трикотажа. Контрастная подкладка. Нескользщящая подошва из 100% каучука.</p>
                        </div>
                    </td>
                    <td>
                        <span>10<span>шт.</span></span>
                    </td>
                    <td>
                        <span class="product-price new-price">900<span class="price-parameter">шт.</span></span>
                        <span class="product-price old-price">1250<span class="price-parameter">шт.</span></span>
                        <div class="discount"><span>-25%</span></div>
                    </td>
                </tr>
                <tr>
                    <td><a href=""><img src="http://tp43.sky-cms.ru/public/css/img/tapki1.jpg" /></a></td>
                    <td>
                        <div class="product-description">
                            <a href="">Название товара</a>
                            <p>Женские тапочки-балетки. Верх выполнен из 100% хлопкового трикотажа. Контрастная подкладка. Нескользщящая подошва из 100% каучука.</p>
                        </div>
                    </td>
                    <td>
                        <span>10<span>шт.</span></span>
                    </td>
                    <td>
                        <span class="product-price new-price">900<span class="price-parameter">шт.</span></span>
                    </td>
                </tr>
            </table>
            <div class="total-cost">
                <span>Общая стоимость:<span>2400 руб.</span></span>
            </div>
        </div>
        <div class="totals">
            <div class="personal-info">
                <h3>Контактные данные:</h3>
                <span><span>ФИО:</span>Иванов Петр Иванович</span>
                <span><span>Тел.:</span>+7 910 999-56-78</span>
                <span><span>E-mail:</span>petr76@mail.ru</span>
            </div>
            <div class="delivery-info">
                <span class="h3">Получение товара:<span>самовывоз</span></span>
                <span class="h3">Оплата:<span>наличными при получении</span></span>
            </div>
        </div>
        <a href="" class="btn back-to-site">Вернуться на сайт</a>
    </div-->
</div>