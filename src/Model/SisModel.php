<?php

namespace App\Model;

class SisModel extends \Sky4\Model\Composite {

    use Component\IdTrait;

    public function fields() {
        return [
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
