<?php

namespace App\Model;

class Issue extends \Sky4\Model\Composite {

    use Component\IdTrait,
        Component\ActiveTrait,
        Component\NameTrait,
        Component\TimestampActionTrait,
        Component\ImageTrait,
        Component\FullImageTrait;

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
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'id_material'
            ],
            'id_issue' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'id_issue',
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'id_issue'
            ],
            'id_city' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'Город выпуска',
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'id_city'
            ],
            'name' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'Заголовок',
                    'type' => 'string(255)',
                ],
                'elem' => 'text_field',
                'label' => 'module',
                'params' => [
                    'rules' => ['length' => ['max' => 255, 'min' => 1], 'required']
                ]
            ],
            'number' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'Номер выпуска',
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'number'
            ],
            'short_text' => [
                'col' => [
                    'flags' => 'not_null',
                    'type' => 'text_2'
                ],
                'elem' => 'tiny_mce',
                'label' => 'Краткое описание выпуска',
                'params' => [
                    'parser' => true
                ]
            ],
            'text' => [
                'col' => [
                    'flags' => 'not_null',
                    'type' => 'text_2'
                ],
                'elem' => 'tiny_mce',
                'label' => 'Текст выпуска',
                'params' => [
                    'parser' => true
                ]
            ],
        ];
    }

}
