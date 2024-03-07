<?php

namespace App\Model;

class FirmLink extends \Sky4\Model\Composite {

    use Component\IdTrait,
        Component\IdFirmTrait;

    public function cols() {
        return [
            'id_firm' => ['label' => 'Фирма']
        ];
    }

    public function defaultOrder() {
        return ['id' => 'DESC'];
    }

    public function fields() {
        return [
            'catalog_ids' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'type' => 'string(255)'
                ],
                'elem' => 'model_autocomplete',
                'label' => 'Рубрикатор',
                'params' => [
                    'model_alias' => 'price-catalog',
                    'field_name' => 'web_many_name_for_catalogs',
                    'rel_model_alias' => 'firm-link-catalog',
                    'rel_field_name_1' => 'id_firm_link',
                    'rel_field_name_2' => 'id_catalog',
                    'rel_model_field_id' => 'id'
                ]
            ],
            'subgroup_ids' => [
                'col' => [
                    'default_val' => '76',
                    'flags' => 'not_null',
                    'type' => 'string(1000)'
                ],
                'elem' => 'model_autocomplete',
                'label' => 'Выбор подгрупп',
                'params' => [
                    'model_alias' => 'price-catalog',
                    'field_name' => 'web_many_name_for_subgroups',
                    'rel_model_alias' => 'firm-link-group',
                    'rel_field_name_1' => 'id_firm_link',
                    'rel_field_name_2' => 'id_subgroup',
                    'rel_model_field_id' => 'id_subgroup'
                ]
            ],
            'search_phrases' => [
                'col' => \Sky4\Db\ColType::getString(255),
                'elem' => 'text_field',
                'label' => 'Поисковые фразы (через запятую)'
            ],
        ];
    }

    public function filterFields() {
        return [
            'id_firm' => [
                'elem' => 'drop_down_list',
                'label' => 'Фирма',
                'options' => $this->idFirmComponent()->getFirmNamesForFilter(),
                'field_name' => 'id_firm'
            ],
        ];
    }
    
    public function afterInsert(&$vals, $parent_object = null) {
        $this->saveRels($vals);
        return parent::afterInsert($vals, $parent_object);
    }

    public function afterUpdate(&$vals) {
        $this->saveRels($vals);
        return parent::afterUpdate($vals);
    }

    private function saveRels(&$vals) {
        $fields = $this->getFields();
        if (isset($fields['subgroup_ids']) && isset($fields['subgroup_ids']['elem']) && ($fields['subgroup_ids']['elem'] === 'model_autocomplete')) {
            $elem = \Sky4\Utils::getElemClass($fields['subgroup_ids']['elem']);
            $elem->setModel($this)
                    ->setParams(isset($fields['subgroup_ids']['params']) ? $fields['subgroup_ids']['params'] : [])
                    ->saveRels('subgroup_ids', $fields['subgroup_ids'], $vals);
        }

        if (isset($fields['catalog_ids']) && isset($fields['catalog_ids']['elem']) && ($fields['catalog_ids']['elem'] === 'model_autocomplete')) {
            $elem = \Sky4\Utils::getElemClass($fields['catalog_ids']['elem']);
            $elem->setModel($this)
                    ->setParams(isset($fields['catalog_ids']['params']) ? $fields['catalog_ids']['params'] : [])
                    ->saveRels('catalog_ids', $fields['catalog_ids'], $vals);
        }

        return $this;
    }
    
    public function delete() {
		$flg = new FirmLinkGroup();
		$all = $flg->reader()
				->setWhere(['AND', 'id_firm_link = :id'], [':id' => $this->id()])
				->objects();
		foreach ($all as $ob) {
			$ob->delete();
		}

		$flc = new FirmLinkCatalog();
		$all = $flc->reader()->setWhere(['AND', 'id_firm_link = :id'], [':id' => $this->id()])
				->objects();
		foreach ($all as $ob) {
			$ob->delete();
		}

		return parent::delete();
	}

}
