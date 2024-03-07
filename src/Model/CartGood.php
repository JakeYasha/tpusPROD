<?php

namespace App\Model;

class CartGood extends \Sky4\Model\Composite {

    use Component\IdTrait,
        Component\TimestampActionTrait,
        Component\ActiveTrait,
        Component\IdFirmTrait,
        Component\TextTrait;

    public function link() {
        return '/cart_good/' . $this->id();
    }

    public function title() {
        return 'Позиция';
    }

    public function filterFields() {
        
    }

    public function cols() {
        
    }

    public function fields() {
        $c = $this->fieldPropCreator();

		return [
			'id' => $c->intField('ID', 8, ['rules' => ['int']], ['flags' => 'auto_increment not_null primary_key unsigned']),
            'id_cart' => $c->intField('ID корзины', 11, ['rules' => ['int']], ['flags' => 'not_null key unsigned']),
            'price_id' => $c->intField('ID товара', 11, ['rules' => ['int']], ['flags' => 'not_null key unsigned']),
            'id_firm' => $c->intField('ID фирмы', 11, ['rules' => ['int']], ['flags' => 'not_null key unsigned']),
			//
			'flag_is_active' => $c->checkBox('Активен'),
			//
            'name' => $c->stringField('Название', 500),
            'count' => $c->intField('Количество товара', 11, ['rules' => ['int']], ['flags' => 'not_null key unsigned']),
		];
    }

    public function getByIdCart($id_cart, $price_id) {
        return $this->reader()
                        ->setWhere(['AND', '`id_cart` = :id_cart', '`price_id` = :price_id'], [':id_cart' => $id_cart, ':price_id' => $price_id])
                        ->objectByConds();
    }

    public function getCartGoodCost() {
        $_price = new Price($this->val('price_id'));
        return $_price->val('price');
    }

}
