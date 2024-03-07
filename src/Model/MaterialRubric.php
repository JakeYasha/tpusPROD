<?php

namespace App\Model;

class MaterialRubric extends \Sky4\Model\Composite {

    use Component\IdTrait;

    public function fields() {
        return [
            'id_rubric' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'ID рубрики',
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'id_rubric'
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
        ];
    }

}
