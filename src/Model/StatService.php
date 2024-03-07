<?php

namespace App\Model;

class StatService extends \Sky4\Model\Composite {

    public function fields() {
        return [
            'id_service' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null primary_key unsigned',
                    'name' => 'id_service',
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'id_service'
            ],
            'active' => [
                'col' => [
                    'default_val' => '1',
                    'flags' => '',
                    'name' => 'active',
                    'type' => 'int_1',
                ],
                'elem' => 'text_field',
                'label' => 'active'
            ],
            'status' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'status',
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'status'
            ],
        ];
    }
	public function idFieldsNames() {
        return ['id_service'];
    }

}
