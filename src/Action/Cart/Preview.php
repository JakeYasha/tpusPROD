<?php

namespace App\Action\Cart;

use App\Model\Price as Price;

class Preview extends \App\Action\Cart {

    public function execute() {
        $this->model()->getByCookie();

        $_cart_good_cost = 0;
        $items = [];
        foreach ($this->model()->cart_goods as $item) {
            $price = new Price($item->val('price_id'));
            $_item = $price->prepare();
            $_item['count'] = $item->val('count');
            $_cart_good_cost += (float)trim(str_replace(' ', '', str_replace(',', '.', $_item['price']))) * (float)$_item['count'];
            $items []= $_item;
        }
        
        return $this->view()
                        ->set('cart_good_count', $this->model()->cart_goods_count)
                        ->set('cart_good_cost', $_cart_good_cost)
                        ->set('items', $items)
                        ->setTemplate('preview')
                        ->render();
    }

}
