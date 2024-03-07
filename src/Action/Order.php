<?php

namespace App\Action;

use App\Model\Cart as CartModel;
use App\Model\Order as OrderModel;
use App\Model\OrderUser;
use App\Model\Firm as FirmModel;
use App\Model\Price;

class Order extends \App\Classes\Action {

    public function __construct($order = null) {
        parent::__construct();
        $this->setModel($order != null ? $order : new OrderModel());
    }

    public function execute() {
        $params = app()->request()->processPostParams([
            'user' => ['type' => 'string'],
            'order' => ['type' => 'int'],
            'step' => ['type' => 'int']
        ]);
        
        $this->model()->get($params['order'], $params['user']);

        if (!$this->model()->exists()) {
            throw new \Sky4\Exception(\Sky4\Exception::TYPE_BAD_URL);
        }

        switch ($params['step']) {
            default:
            /*case '1':
                $order_user = (new OrderUser())->getByCookie($params['user']);
                if (!$order_user->exists()) {
                    throw new \Sky4\Exception(\Sky4\Exception::TYPE_BAD_URL);
                }
                
                app()->frontController()->layout()->setTemplate('order');
                return $this->view()
                                ->setTemplate('user','order')
                                ->set('name', $order_user->val('name'))
                                ->set('phone', $order_user->val('phone'))
                                ->set('email', $order_user->val('email'))
                                ->set('subscribed', $order_user->val('subscribed'))
                                ->set('user_hash', $order_user->val('cookie_hash'))
                                ->set('order_id', $this->model()->id())
                                ->save();*/
            case '2':
                $_params = app()->request()->processPostParams([
                    'name' => ['type' => 'string'],
                    'phone' => ['type' => 'string'],
                    'email' => ['type' => 'string'],
                    'subscribed' => ['type' => 'string']
                ]);
                
                $order_user = (new OrderUser())->getByCookie($params['user']);
                if (!$order_user->exists()) {
                    throw new \Sky4\Exception(\Sky4\Exception::TYPE_BAD_URL);
                }

                $order_user->update([
                    'name' => $_params['name'],
                    'phone' => $_params['phone'],
                    'email' => $_params['email'],
                    'subscribed' => $_params['subscribed'] ? 1 : 0
                ]);
                $firm = [];
                $order_goods = $this->model()->getOrderGoods();
                if (count($order_goods) > 0 && (int) $order_goods[0]['id_firm'] > 0) {
                    $firm = new FirmModel($order_goods[0]['id_firm']);
                }

                if (!$firm) {
                    throw new \Sky4\Exception(\Sky4\Exception::TYPE_BAD_URL);
                }

                $delivery = [];
                if ($firm->hasDelivery()) {
                    $delivery = $firm->getDelivery();
                }

                $payment = [];

                app()->frontController()->layout()->setTemplate('order');
                return $this->view()
                                ->setTemplate('delivery_and_pay')
                                ->set('delivery', $delivery)
                                ->set('payment', $payment)
                                ->set('firm', $firm)
                                ->set('order', $this->model())
                                ->set('user_hash', $order_user->val('cookie_hash'))
                                ->set('order_id', $this->model()->id())
                                ->save();
            case '3':
                $_params = app()->request()->processPostParams([
                    'delivery' => ['type' => 'string'],
                    'payment' => ['type' => 'string']
                ]);
                $order_user = (new OrderUser())->getByCookie($params['user']);
                if (!$order_user->exists()) {
                    throw new \Sky4\Exception(\Sky4\Exception::TYPE_BAD_URL);
                }

                $this->model()->update([
                    'delivery' => $_params['delivery'],
                    'payment' => $_params['payment']
                ]);
                $firm = [];
                $_order_goods = $this->model()->getOrderGoods();
                $order_goods['cost'] = 0;
                foreach ($_order_goods as $item) {
                    if (!$firm) {
                        $firm = new FirmModel($item['id_firm']);
                    }

                    $order_goods['cost'] += (float) trim(str_replace(' ', '', str_replace(',', '.', $item['price']))) * (float) $item['count'];
                    $order_goods['items'] [] = $item;
                }

                if (!$firm) {
                    throw new \Sky4\Exception(\Sky4\Exception::TYPE_BAD_URL);
                }

                $delivery_types = \App\Model\FirmDelivery::types();
                $delivery = $_params['delivery'] || $_params['delivery'] == 4 ? $delivery_types[$_params['delivery']] : 'Самовывоз из офиса, магазина, ' . $firm->address();

                $payment = 'Наличными при получении';

                /*
                 * Завершение заказа:
                 * 1. Удаляем позиции заказа (по id_firm) из корзины
                 * 2. Отправляем уведомление о заказе клиенту на почту
                 * 3. Отправляем подтверждение заказа покупателю на почту
                 * 4. Отправляем уведомление менеджеру фирмы
                 */

                $cart = (new CartModel())->getByCookie();
                $cart->deleteCartGoodsByFirm($firm->id());

                $is_cart_empty = count($cart->cart_goods) == 0 ? true : false;


                //отправляем заказ покупателю
                if ($order_user->val('email')) {
                    $timestamp = new \Sky4\Helper\DateTime($this->model()->val('timestamp_inserting'));
                    app()->email()
                            ->setSubject('Вы оформили заказ ' . $timestamp->format('d.m.Y') . ' на сайте tovaryplus.ru на сумму ' . $order_goods['cost'] . ' руб.')
                            //->setTo(app()->config()->get('app.email.administrator'))
                            //->setTo($this->model()->val('email'))
                            ->setTo('vae@tovaryplus.ru')
                            ->setModel($this->model())
                            ->setTemplate('email_to_customer', 'order')
                            ->setParams([
                                'firm_name' => $firm->name(),
                                'firm_link' => $firm->linkItem(),
                                'order_user' => $order_user,
                                'order_date' => $timestamp->format('d.m.Y'),
                                'order_id' => $this->model()->id(),
                                'order_goods' => $order_goods,
                                'order' => $this->model(),
                                'delivery' => $delivery,
                                'payment' => $payment
                            ])
                            ->sendToQuery();
                }

                //отправляем заказ клиенту
                if ($order_user->val('email')) {
                    $timestamp = new \Sky4\Helper\DateTime($this->model()->val('timestamp_inserting'));
                    $firm_user = (new \App\Model\FirmUser())->getByFirm($firm);
                    if ($firm_user && $firm_user->exists()) {
                        app()->email()
                                ->setSubject('Сформирован заказ ' . $timestamp->format('d.m.Y') . ' на сайте tovaryplus.ru на сумму ' . $order_goods['cost'] . ' руб.')
                                //->setTo(app()->config()->get('app.email.administrator'))
                                ->setTo($firm_user->val('email') ? $firm_user->val('email') : $firm->email())
                                ->setModel($this->model())
                                ->setTemplate('email_to_client', 'order')
                                ->setParams([
                                    'firm_name' => $firm->name(),
                                    'firm_link' => $firm->linkItem(),
                                    'order_user' => $order_user,
                                    'order_date' => $timestamp->format('d.m.Y'),
                                    'order_id' => $this->model()->id(),
                                    'order_goods' => $order_goods,
                                    'order' => $this->model(),
                                    'delivery' => $delivery,
                                    'payment' => $payment
                                ])
                                ->sendToQuery();
                    }
                }

                //отправляем заказ менеджеру
                if ($order_user->val('email')) {
                    $timestamp = new \Sky4\Helper\DateTime($this->model()->val('timestamp_inserting'));
                    $manager = (new \App\Model\FirmManager())->getByFirm($firm);
                    if ($manager && $manager->exists()) {
                        app()->email()
                                ->setSubject('Вы оформили заказ ' . $timestamp->format('d.m.Y') . ' на сайте tovaryplus.ru на сумму ' . $order_goods['cost'] . ' руб.')
                                //->setTo(app()->config()->get('app.email.administrator'))
                                ->setTo($manager->val('email'))//$this->model()->val('email'))
                                ->setModel($this->model())
                                ->setTemplate('email_to_manager', 'order')
                                ->setParams([
                                    'firm_name' => $firm->name(),
                                    'firm_link' => $firm->linkItem(),
                                    'order_user' => $order_user,
                                    'order_date' => $timestamp->format('d.m.Y'),
                                    'order_id' => $this->model()->id(),
                                    'order_goods' => $order_goods,
                                    'order' => $this->model(),
                                    'delivery' => $delivery,
                                    'payment' => $payment
                                ])
                                ->sendToQuery();
                    }
                }

                app()->frontController()->layout()->setTemplate('order');
                return $this->view()
                                ->setTemplate('order')
                                ->set('delivery', $delivery)
                                ->set('payment', $payment)
                                ->set('firm', $firm)
                                ->set('order', $this->model())
                                ->set('is_cart_empty', $is_cart_empty)
                                ->set('order_user', $order_user)
                                ->set('order_goods', $order_goods)
                                ->set('order_user', $order_user)
                                ->set('order_id', $this->model()->id())
                                ->save();
            /*default:
                $_order_goods = $this->model()->getOrderGoods();

                $order_goods = [];
                $order_goods['cost'] = 0;
                $firm = null;
                foreach ($_order_goods as $item) {
                    if ($firm == null) {
                        $firm = new FirmModel($item['id_firm']);
                    }
                    $order_goods['cost'] += trim(str_replace(' ', '', $item['price'])) * $item['count'];
                    $order_goods['items'] [] = $item;
                }

                app()->frontController()->layout()->setTemplate('order');
                return $this->view()
                                ->setTemplate('order')
                                ->set('order', $this->model())
                                ->set('order_goods', $order_goods)
                                ->save();*/
        }
    }

    /**
     * 
     * @return OrderModel
     */
    public function model() {
        return parent::model();
    }

}
