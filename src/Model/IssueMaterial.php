<?php

namespace App\Model;

class IssueMaterial extends \Sky4\Model\Composite {

    use Component\IdTrait,
        Component\ActiveTrait;

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
            'timestamp_inserting' => [
                'col' => [
                    'default_val' => date("Y-m-d H:i:s"),
                    'flags' => 'not_null',
                    'name' => 'timestamp_inserting',
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'timestamp_inserting'
            ],
            
            
            
            
        ]; 
    }

}
