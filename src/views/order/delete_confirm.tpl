<div class="cart-block">
    <h1>Удаление заказа</h1>
    <div class="order-step">
        <form action="/order/delete/" method="GET">
            <input type="hidden" name="user" value="<?= $user->val('cookie_hash') ?>"/>
            <input type="hidden" name="order" value="<?= $order_id ?>"/>
            <input type="hidden" name="delete" value="1"/>

            <h3>Подтвердите удаление заказа № <?= $order_id ?></h3>

            <div class="buttons-wrapper">
                <button class="btn">Удалить</button>
            </div>
        </form>
    </div>
</div>