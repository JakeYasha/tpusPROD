<?php

namespace App\Model;

use App\Model\Order as OrderModel;

class OrderUser extends \Sky4\Model\Composite {

    use Component\IdTrait,
        Component\UserTrait,
        Component\IdFirmTrait;

    public $orders = [];

    public function cols() {
        return [
            'email' => ['label' => 'Email']
        ];
    }

    public function orderableFieldsNames() {
        return array_keys($this->cols());
    }

    public function title() {
        return $this->exists() ? $this->name() : 'Пользователи';
    }

    public function fields() {
        $c = $this->fieldPropCreator();

		return [
			'id' => $c->intField('ID', 8, ['rules' => ['int']], ['flags' => 'auto_increment not_null primary_key unsigned']),
            'name' => $c->stringField('Название', 128),
            'email' => $c->stringField('Email', 128),
            'phone' => $c->stringField('Телефон', 128),
            'cookie_hash' => $c->stringField('Хэш', 32),
            'flag_is_active' => $c->checkBox('Активен'),
			//
			'subscribed' => $c->checkBox('Подписан на рассылку')
		];   
    }

    public function hash() {
        return md5(app()->request()->getRemoteAddr() . app()->request()->getUserAgent() . time());
    }

    private function getByHash($hash) {
        $this->reader()
                ->setWhere(['AND', 'cookie_hash = :hash'], [':hash' => $hash])
                ->objectByConds();
        if (!$this->exists())
            return null;

        $this->getOrderUserOrders();
        return $this;
    }

    public function getByCookie($cookie_hash = '') {
        if ($cookie_hash == '') {
            $cookie_hash = app()->cookie()->get('_order_user_tp');
            if (!$cookie_hash) {
                $cookie_hash = $this->hash();

                app()->cookie()->setExpireYear()->set('_order_user_tp', $cookie_hash);
            }
        }

        if ($this->getByHash($cookie_hash) === null) {
            $this->insert([
                'flag_is_active' => 1,
                'last_activity_timestamp' => \Sky4\Helper\DateTime::now()->format(),
                'cookie_hash' => $cookie_hash
            ]);
        }

        return $this;
    }

    private function getOrderUserOrders() {
        $this->orders = (new OrderModel())
                ->reader()
                ->setWhere(['AND', 'order_user_id = :order_user_id'], [':order_user_id' => $this->id()])
                ->objects();
    }

    public function filterFields() {
        return [
            'email' => [
                'elem' => 'text_field',
                'label' => 'Email',
                'cond' => 'LIKE',
                'field_name' => 'email'
            ]
        ];
    }

    public function filterFormStructure() {
        return [
            ['type' => 'field', 'name' => 'email']
        ];
    }

}
