<?php

namespace App\Model;

class FirmUser extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\UserTrait,
	 Component\IdFirmTrait;

	public function cols() {
		return [
			'email' => ['label' => 'Email'],
			//'virtual_id_firm' => ['label' => 'Фирма'],
			'last_activity_timestamp' => ['label' => 'Последний заход', 'style_class' => 'date-time']
		];
	}

	public function orderableFieldsNames() {
		return array_keys($this->cols());
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Пользователи';
	}

	public function filterFields() {
		return [
//			'virtual_id_firm' => [
//				'elem' => 'drop_down_list',
//				'label' => 'Фирма',
//				'options' => $this->idFirmComponent()->getFirmNamesForFilter(),
//				'cond' => '=',
//				'field_name' => 'virtual_id_firm',
//				'assembler' => [
//					'class_name' => 'VirComponent\ualIdFirmTrait',
//					'method_name' => 'assembleFilterConds'
//				]
//			],
			'email' => [
				'elem' => 'text_field',
				'label' => 'Email',
				'cond' => 'LIKE',
				'field_name' => 'email'
			]
		];
	}

	public function filterFormStructure() {
		return [
			//['type' => 'field', 'name' => 'virtual_id_firm'],
			['type' => 'field', 'name' => 'email'],
				//['type' => 'component', 'name' => 'TimestampInterval'],
				//['type' => 'field', 'name' => 'timestamp_ending']
		];
	}

	public function afterInsert(&$vals, $parent_object = null) {
		if (isset($vals['id_firm'])) {
			$firm = new Firm();
			$firm->getByIdFirm($vals['id_firm']);
			if ($firm->exists()) {
				$firm->update(['id_firm_user' => $this->id()]);
			}
		}
		return parent::afterInsert($vals, $parent_object);
	}

	public function beforeDelete() {
		if (isset($this->vals['id_firm'])) {
			$firm = new Firm();
			$firm->getByIdFirm($this->vals['id_firm']);
			if ($firm->exists()) {
				$firm->update(['id_firm_user' => 0]);
			}
		}
		return $this;
	}

}
