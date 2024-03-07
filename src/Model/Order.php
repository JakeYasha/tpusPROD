<?php

namespace App\Model;

use App\Model\OrderGood as OrderGoodModel;
use App\Model\OrderUser as OrderUserModel;
use App\Model\CartGood as CartGoodModel;
use App\Model\Cart as CartModel;
use App\Model\StsPrice as Price;

class Order extends \Sky4\Model\Composite {

    use Component\IdTrait,
        Component\TimestampActionTrait,
        Component\TextTrait;

    public $order_user = null;

    public function link() {
        if ($this->order_user !== null) {
            return '/order/?user=' . $this->order_user->val('cookie_hash') . '&order=' . $this->val('id');
        } else {
            return '/cart/';
        }
    }

    public function get($order_id, $user_hash) {
        $order_user = (new OrderUser())->getByCookie($user_hash);

        if (!$order_user->exists()) {
            throw new \Sky4\Exception(\Sky4\Exception::TYPE_BAD_URL);
        }
        return $this->reader()
                        ->setWhere(['AND', 'id = :id', 'order_user_id = :order_user_id'], [':id' => $order_id, ':order_user_id' => $order_user->id()])
                        ->objectByConds();
    }

    public function title() {
        return 'Заказ';
    }

    public function filterFields() {
        
    }

    public function cols() {
        
    }

    public function fields() {
        $c = $this->fieldPropCreator();

		return [
			'id' => $c->intField('ID', 8, ['rules' => ['int']], ['flags' => 'auto_increment not_null primary_key unsigned']),
            'order_user_id' => $c->intField('ID пользователя', 11, ['rules' => ['int']], ['flags' => 'not_null key unsigned']),
            'delivery' => $c->intField('Доставка', 11, ['rules' => ['int']], ['flags' => 'not_null key unsigned']),
            'payment' => $c->intField('Оплата', 11, ['rules' => ['int']], ['flags' => 'not_null key unsigned']),
			//
			'flag_is_active' => $c->checkBox('Активен'),
			//
            'text' => $c->textArea('Данные заказа')
		];        
    }

    public function getOrderGoods() {
        $order_goods = unserialize(base64_decode($this->val('text')));

        return $order_goods;
    }

    public function getOrderGoodCount() {
        $order_goods_count = count(getOrderGoods());

        return $order_goods_count;
    }

    public function getOrderGoodCost() {
        $order_goods = getOrderGoods();
        $cost = 0;
        foreach ($order_goods as $price) {
            if ($price->val('price') > 0) {
                $cost += $price->val('price');
            }
        }
        return $cost;
    }

}
