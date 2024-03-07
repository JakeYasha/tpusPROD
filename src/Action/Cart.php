<?php

namespace App\Action;

use App\Model\Cart as CartModel;
use App\Model\Firm as FirmModel;
use App\Model\Image as ImageModel;
use App\Model\Price as Price;

class Cart extends \App\Classes\Action {

    public function __construct() {
        parent::__construct();
        $this->setModel(new CartModel());
    }

    public function execute() {
        $this->model()->getByCookie();

        $_by_firm_items = [];
        foreach ($this->model()->cart_goods as $item) {
            $price = new Price($item->val('price_id'));
            if (!$price->exists())
                continue;

            $firm_key = $price->val('id_firm');

            if (!isset($_by_firm_items[$firm_key])) {
                $_by_firm_items[$firm_key] = [
                    'items' => [],
                    'cost' => 0,
                    'firm' => new FirmModel($price->val('id_firm'))
                ];
            }
            $_item = $price->prepare();
            $_item['count'] = $item->val('count');

            $_by_firm_items[$firm_key]['cost'] += (float) trim(str_replace(' ', '', str_replace(',', '.', $_item['price']))) * (float) $_item['count'];

            $_by_firm_items[$firm_key]['items'] [] = $_item;
        }

        app()->frontController()->layout()->setTemplate('cart');
        return $this->view()
                        ->setTemplate(empty($this->model()->cart_goods) ? 'empty' : 'index', 'cart')
                        ->set('cart_hash', $this->model()->val('cookie_hash'))
                        ->set('firm_items', $_by_firm_items)
                        ->set('items_count', $this->model()->cart_goods_count)
                        ->set('items_cost', $this->model()->cart_total_cost)
                        ->save();
    }

}
