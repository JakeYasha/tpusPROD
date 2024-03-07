<?php

namespace App\Model;

class AdvertModuleFirmType extends \Sky4\Model\Composite {

	use Component\IdTrait;

	public function fields() {
		return [
			'id_advert_module' => [
				'col' => [
					'default_val' => '0.000000',
					'flags' => 'not_null',
					'type' => 'double(9,6)'
				],
				'elem' => 'hidden_field'
			],
			'id_firm_type' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null unsigned',
					'type' => 'int_8'
				],
				'elem' => 'hidden_field'
			],
			'active' => [
				'elem' => 'single_check_box'
			]
		];
	}

	public function getFirmTypeIdsByAdvertModuleId($advert_module_id) {
		return array_keys(
				$this->reader()
						->setWhere(['AND', 'id_advert_module = :id_advert_module'], [':id_advert_module' => $advert_module_id])
						->rowsWithKey('id_firm_type')
		);
	}

	public function getAdvertModuleIdsByFirmTypes($firm_types) {
		$firm_types_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($firm_types, 'id_firm_type');
		$advert_module_ids = array_keys($this->reader()->setWhere($firm_types_conds['where'], $firm_types_conds['params'])
						->setOrderBy('RAND()')
						->rowsWithKey('id_advert_module'));

		return array_unique($advert_module_ids);
	}

}
