<?php

namespace App\Action\Order;

use App\Model\Cart as CartModel;

class Create extends \App\Action\Order {

    public function execute() {
        $params = app()->request()->processPostParams([
            'cart_hash' => ['type' => 'string'],
            'firm_id' => ['type' => 'int'],
            'step' => ['type' => 'int']
        ]);
        $cart = (new CartModel())->getByHash($params['cart_hash']);

        if (!$cart || !$cart->exists()) {
            throw new \Sky4\Exception('Невозможно создать заказ');
        }
        $cart_goods = $cart->getCartGoodsByFirm($params['firm_id']);

        $prices = [];
        if (count($cart_goods) > 0) {
            foreach ($cart_goods as $cart_good) {
                $_price = new \App\Model\Price($cart_good->val('price_id'));
                if ($_price->exists()) {
                    $price = $_price->prepare();
                    $price['count'] = $cart_good->val('count');
                    $prices [] = $price;
                }
            }
        }
        $_prices = base64_encode(serialize($prices));

        $order_user = (new \App\Model\OrderUser())->getByCookie();

        $order = (new \App\Model\Order());
        $order->insert([
            'flag_is_active' => 1,
            'text' => $_prices,
            'order_user_id' => $order_user->id(),
            'timestamp_inserting' => \Sky4\Helper\DateTime::now()->format()
        ]);

        app()->frontController()->layout()->setTemplate('order');
        return $this->view()
                        ->setTemplate('user', 'order')
                        ->set('name', $order_user->val('name'))
                        ->set('phone', $order_user->val('phone'))
                        ->set('email', $order_user->val('email'))
                        ->set('subscribed', $order_user->val('subscribed'))
                        ->set('user_hash', $order_user->val('cookie_hash'))
                        ->set('order_id', $order->id())
                        ->save();
    }

}
