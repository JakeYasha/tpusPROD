<?php

namespace App\Model;

class BannerCatalog extends \Sky4\Model\Composite {

    use Component\IdTrait;

    public function fields() {
        return [
            'id_banner' => [
                'col' => \Sky4\Db\ColType::getInt(8),
                'elem' => 'hidden_field'
            ],
            'id_catalog' => [
                'col' => \Sky4\Db\ColType::getInt(8),
                'elem' => 'hidden_field'
            ]
        ];
    }

    public function getCatalogIdsByBannerId($banner_id) {
        return array_keys(
                $this->reader()
                        ->setSelect(['id_catalog'])
                        ->setWhere(['AND', 'id_banner = :id_banner'], [':id_banner' => $banner_id])
                        ->rowsWithKey('id_catalog')
        );
    }

}
