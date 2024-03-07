<?php

namespace App\Model;

class FirmLinkCatalog extends \Sky4\Model\Composite {

    use Component\IdTrait;

    public function fields() {
        return [
            'id_firm_link' => [
                'col' => \Sky4\Db\ColType::getInt(8),
                'elem' => 'hidden_field'
            ],
            'id_catalog' => [
                'col' => \Sky4\Db\ColType::getInt(8),
                'elem' => 'hidden_field'
            ]
        ];
    }

}
