<?php

namespace App\Action\Order;

class Delete extends \App\Action\Order {

    public function execute() {
        $params = app()->request()->processGetParams([
            'user' => ['type' => 'string'],
            'order' => ['type' => 'int'],
            'delete' => ['type' => 'int']
        ]);

        $order_user = (new \App\Model\OrderUser())->getByCookie($params['user']);
        if (!$order_user->exists()) {
            throw new \Sky4\Exception('Такого пользователя не существует');
        }
        $order = (new \App\Model\Order())->get($params['order'], $params['user']);

        if (!$order || !$order->exists()) {
            throw new \Sky4\Exception('Такого заказа не существует');
        }

        $hours = (int) app()->config()->get('app.order.cancel.hours', 1);

        $orderdatetime = date('Y-m-d H:i:s', strtotime($order->val('timestamp_inserting') . " +{$hours} hours"));
        $datetime = \Sky4\Helper\DateTime::now()->format('Y-m-d H:i:s');

        app()->frontController()->layout()->setTemplate('order');
        if ($orderdatetime >= $datetime && !$params['delete']) { // Подтверждение удаления заказа
            return $this->view()
                            ->set('user', $order_user)
                            ->set('order', $order)
                            ->set('order_id', $params['order'])
                            ->setTemplate('delete_confirm', 'order')->save();
        } else if ($orderdatetime >= $datetime && $params['delete']) { // Удаление заказа
            $order->delete();
            return $this->view()
                            ->set('order_id', $params['order'])
                            ->set('user', $order_user)
                            ->setTemplate('delete_success', 'order')->save();
        } else { // Ошибка удаления заказа
            return $this->view()
                            ->set('order_id', $params['order'])
                            ->setTemplate('delete_fail', 'order')->save();
        }
    }

}
