<?php

namespace App\Model;

use App\Model\CartGood as CartGoodModel;

class Cart extends \Sky4\Model\Composite {

    use Component\IdTrait,
        Component\TimestampActionTrait,
        Component\TextTrait;

    public $cart_goods = [];
    public $cart_goods_count = 0;
    public $cart_total_cost = 0;

    public function link() {
        return '/cart/' . $this->hash();
    }

    public function title() {
        return 'Корзина';
    }

    public function filterFields() {
        
    }

    public function cols() {
        
    }

    public function fields() {
        $c = $this->fieldPropCreator();

        return [
            'id' => $c->intField('ID', 8, ['rules' => ['int']], ['flags' => 'auto_increment not_null primary_key unsigned']),
            //
            'flag_is_active' => $c->checkBox('Активен'),
            //
            'cookie_hash' => $c->stringField('Страна производства', 32),
            //
            'text' => $c->textArea('Данные заказа')
        ];
    }

    public function hash() {
        return md5(app()->request()->getRemoteAddr() . app()->request()->getUserAgent() . time());
    }

    public function getByHash($hash) {
        $this->reader()
                ->setWhere(['AND', 'cookie_hash = :hash'], [':hash' => $hash])
                ->objectByConds();
        if (!$this->exists())
            return null;

        $this->getCartGoods();
        $this->getCartGoodCount();
        $this->getCartTotalCost();
        return $this;
    }

    public function getByCookie() {
        $cookie_hash = app()->cookie()->get('_cart_tp');
        if (!$cookie_hash) {
            $cookie_hash = $this->hash();

            app()->cookie()->setExpireYear()->set('_cart_tp', $cookie_hash);
        }

        if ($this->getByHash($cookie_hash) === null) {
            $timestamp_inserting = new \Sky4\Helper\DateTime($this->val('timestamp_inserting'));
            $this->insert([
                'flag_is_active' => 1,
                'timestamp_inserting' => $timestamp_inserting->timestamp(),
                'cookie_hash' => $cookie_hash
            ]);
        }

        return $this;
    }

    private function getCartGoods() {
        $this->cart_goods = (new CartGoodModel())
                ->reader()
                ->setWhere(['AND', 'id_cart = :id_cart'], [':id_cart' => $this->id()])
                ->objects();
    }

    public function getCartGoodsByFirm($firm_id) {
        $_cart_goods_by_firm = [];
        $_cart_goods = (new CartGoodModel())
                ->reader()
                ->setWhere(['AND', 'id_cart = :id_cart'], [':id_cart' => $this->id()])
                ->objects();
        foreach ($_cart_goods as $_cart_good) {
            $price = new Price($_cart_good->val('price_id'));
            if ($price->exists() && $price->val('flag_is_active') && $price->val('flag_is_available') && $price->val('id_firm') == $firm_id) {
                $_cart_goods_by_firm [] = $_cart_good;
            }
        }

        return $_cart_goods_by_firm;
    }

    public function deleteCartGoodsByFirm($firm_id) {
        $this->cart_goods = [];
        $_cart_goods = (new CartGoodModel())
                ->reader()
                ->setWhere(['AND', 'id_cart = :id_cart'], [':id_cart' => $this->id()])
                ->objects();
        foreach ($_cart_goods as $_cart_good) {
            $price = new Price($_cart_good->val('price_id'));
            if ($price->val('id_firm') == $firm_id) {
                $_cart_good->delete();
            } else {
                $this->cart_goods []= $_cart_good;
            }
        }
    }

    private function getCartGoodCount() {
        $this->cart_goods_count = 0;
        foreach ($this->cart_goods as $cart_good) {
            $this->cart_goods_count += $cart_good->val('count');
        }
    }

    private function getCartTotalCost() {
        $this->cart_total_cost = 0;
        foreach ($this->cart_goods as $cart_good) {
            $this->cart_total_cost += $cart_good->getCartGoodCost();
        }
    }

}
