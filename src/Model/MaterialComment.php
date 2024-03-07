<?php

namespace App\Model;
use App\Model\Material;

class MaterialComment extends \Sky4\Model\Composite {

    use Component\IdTrait,
        Component\ActiveTrait,
        Component\DateAndTimeIntervalTrait;

    public function fields() {
        return [
            'id_service' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'ID службы',
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'id_service'
            ],
            'id_material' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'ID материала',
                    'type' => 'int_4',
                ],
                'elem' => 'text_field',
                'label' => 'id_material'
            ],
            'text' => [
                'col' => [
                    'flags' => 'not_null',
                    'type' => 'text_2'
                ],
                'elem' => 'tiny_mce',
                'label' => 'Текст комментария',
                'params' => [
                    'parser' => true
                ]
            ],
            'rating_up' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'Понравилось',
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'rating_up'
            ],
            'rating_down' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'Не понравилось',
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'rating_down'
            ],
        ];
    }

}
