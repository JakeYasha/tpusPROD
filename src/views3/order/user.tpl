<div class="mdc-layout-grid">
    <div class="mdc-layout-grid__inner">
        <div class="mdc-layout-grid__cell--span-12">
            <h1 class="page-title page-title_cart">Оформление заказа</h1>
            <div class="steps">
                <div class="steps__item steps__item_active"><span>1</span>Контактные данные</div>
                <a class="steps__item"><span>2</span>Доставка и оплата</a>
                <a class="steps__item"><span>3</span>Завершение заказа</a>
            </div>
            <div class="order-step">
                <div class="mdc-layout-grid__inner">
                    <div class="mdc-layout-grid__cell--span-6-desktop mdc-layout-grid__cell--span-8-tablet mdc-layout-grid__cell--span-4-phone">
                        <h2 style="margin-bottom: 1.5rem">Контактные данные</h2>
                        <form action="/order/?kcenter=1" method="POST">
                            <input type="hidden" name="user" value="<?=$user_hash?>"/>
                            <input type="hidden" name="order" value="<?=$order_id?>"/>
                            <input type="hidden" name="step" value="2"/>
                            <div class="order-step__item">
                                <label for="name">ФИО</label>
                                <input type="text" class="form__control form__control_modal" id="name" name="name" value="<?=$name?>">
                            </div>
                            <div class="order-step__item">
                                <label for="phone">Телефон</label>
                                <input type="text" class="form__control form__control_modal" id="phone" name="phone" value="<?=$phone?>">
                            </div>
                            <div class="order-step__item">
                                <label for="email">E-mail:</label>
                                <input type="text" class="form__control form__control_modal" id="email" name="email" value="<?=$email?>">
                            </div>
                            <div class="modal__item">
                                <label class="modal__item_checkbox">
                                    <input type="checkbox" name="subscribed" <?=$subscribed ? "checked" : ""?> />
                                    <span>Подписаться на рассылку скидок и специальных предложений</span>
                                </label>
                            </div>
                            <div class="order-step__item" style="margin-top: 1rem">Уже покупали у нас? <a href="" class="order-step__link">Войти на сайт</a></div>
                            <button class="btn btn_primary">Продолжить</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>