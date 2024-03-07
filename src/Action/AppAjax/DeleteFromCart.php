<?php

namespace App\Action\AppAjax;

use \App\Model\Cart as CartModel;
use \App\Model\CartGood as CartGoodModel;

class DeleteFromCart extends \App\Action\AppAjax {

    public function execute() {
        $params = app()->request()->processGetParams([
            'price_id' => ['type' => 'int']
        ]);

        $cart = (new CartModel())->getByCookie();
        $cart_good = (new CartGoodModel())->getByIdCart($cart->id(), $params['price_id']);

        if ($cart_good->exists()) {
            $cart_good->delete();
        }

        die($this->view()
                        ->set('item', $cart_good)
                        ->set('message', 'Товар удален из корзины')
                        ->setTemplate('success', 'cart')->render());
    }

}
