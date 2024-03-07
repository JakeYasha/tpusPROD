<?php

namespace App\Model;

class Kit extends \Sky4\Model\Composite {

    use Component\IdTrait,
        Component\ActiveTrait,
        Component\NameTrait,
        Component\ImageTrait;

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
            'short_text' => [
                'col' => [
                    'flags' => 'not_null',
                    'type' => 'text_2'
                ],
                'elem' => 'tiny_mce',
                'label' => 'Краткое описание подборки',
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
                'label' => 'Текст подборки',
                'params' => [
                    'parser' => true
                ]
            ],
        ];
    }

}
