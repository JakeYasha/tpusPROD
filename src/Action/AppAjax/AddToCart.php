<?php

namespace App\Action\AppAjax;

use \App\Model\Cart as CartModel;
use \App\Model\CartGood as CartGoodModel;

class AddToCart extends \App\Action\AppAjax {

    public function execute() {
        $params = app()->request()->processGetParams([
            'price_id' => ['type' => 'int'],
            'count' => ['type' => 'int']
        ]);
        $price = new \App\Model\Price($params['price_id']);

        if (!$price->exists()) {
            return $this->setResultMessage('Такого товара не существует')
                            ->renderResult();
        }

        $count = $params['count'];
        $firm = new \App\Model\Firm($price->val('id_firm'));

        if (!$firm->exists()) {
            return $this->setResultMessage('Фирма заблокирована')
                            ->renderResult();
        }

        $cart = (new CartModel())->getByCookie();
        $cart_good = (new CartGoodModel())->getByIdCart($cart->id(), $price->id());

        if (!$cart_good->exists()) {
            $cart_good->insert([
                'price_id' => $price->id(),
                'id_cart' => $cart->id(),
                'name' => $price->name(),
                'flag_is_active' => 1,
                'count' => $count
            ]);
        } else {
            die($this->view()
                            ->set('item', $cart_good)
                            ->set('message', 'Товар уже был добавлен в корзину')
                            ->setTemplate('success', 'cart')->render());
        }

        die($this->view()
                        ->set('item', $cart_good)
                        ->set('message', 'Товар добавлен в корзину')
                        ->setTemplate('success', 'cart')->render());
    }

}
