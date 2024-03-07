<?php

namespace App\Model;

/**
 * @deprecated deprecated
 */
class PriceCatalogCount extends \Sky4\Model\Composite {

    use Component\IdTrait;

    public function defaultOrder() {
        return ['`count`' => 'DESC'];
    }

    public function fields() {
        return [
            'id_catalog' => [
                'col' => [
                    'default_val' => '0',
                    'flags' => 'not_null',
                    'name' => 'id_catalog',
                    'type' => 'int_4',
                ],
                'elem' => 'text_field',
                'label' => 'id_catalog'
            ],
            'id_parent' => [
                'col' => [
                    'default_val' => '0',
                    'flags' => 'not_null',
                    'name' => 'id_parent',
                    'type' => 'int_4',
                ],
                'elem' => 'text_field',
                'label' => 'id_parent'
            ],
            'id_firm' => [
                'col' => [
                    'default_val' => '0',
                    'flags' => 'not_null',
                    'name' => 'id_firm',
                    'type' => 'int_4',
                ],
                'elem' => 'text_field',
                'label' => 'id_firm'
            ],
            'count' => [
                'col' => [
                    'default_val' => '0',
                    'flags' => '',
                    'name' => 'count',
                    'type' => 'int_4',
                ],
                'elem' => 'text_field',
                'label' => 'count'
            ],
        ];
    }

}
