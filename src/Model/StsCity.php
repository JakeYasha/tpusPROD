<?php

/**
 * StsCity.
 */
namespace App\Model;
class StsCity extends \Sky4\Model\Composite {

	use Component\PositionWeightTrait;

	public function name() {
		return str()->firstCharsOfWordsToUpper(str()->toLower($this->vals['name']));
	}

	public function cols() {
		return [
			'id_city' => [
				'label' => 'ID'
			],
			'name' => [
				'label' => 'Название'
			],
			'position_weight' => [
				'label' => 'Вес позиции'
			]
		];
	}

	public function orderableFieldsNames() {
		return array_keys($this->cols());
	}

	public function locationId() {
		return $this->val('id_country') == 643 ? $this->val('id_city') : $this->val('id_country') . '-' . $this->val('id_city');
	}

	public function fields() {
		return [
			'id_city' => [
				'col' => [
					'flags' => 'not_null primary_key unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'Код',
				'params' => [
					'rules' => ['int', 'required']
				]
			],
			'id_city_type' => [
				'col' => [
					'flags' => 'not_null index unsigned',
					'type' => 'int_2'
				],
				'elem' => 'text_field',
				'label' => 'Код',
				'params' => [
					'rules' => ['int']
				]
			],
			'id_arial_region' => [
				'col' => [
					'flags' => 'not_null index unsigned',
					'type' => 'int_3'
				],
				'elem' => 'text_field',
				'label' => 'Код',
				'params' => [
					'rules' => ['int']
				]
			],
			'id_region_country' => [
				'col' => [
					'flags' => 'not_null index unsigned',
					'type' => 'int_3'
				],
				'elem' => 'text_field',
				'label' => 'Код',
				'params' => [
					'rules' => ['int']
				]
			],
			'id_country' => [
				'col' => [
					'flags' => 'not_null index unsigned',
					'type' => 'int_3'
				],
				'elem' => 'text_field',
				'label' => 'Код',
				'params' => [
					'rules' => ['int']
				]
			],
			'code' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_2'
				],
				'elem' => 'text_field',
				'label' => 'Код города',
				'params' => [
					'rules' => ['int', 'required']
				]
			],
			'name' => [
				'elem' => 'text_field',
				'label' => 'Название',
				'params' => [
					'rules' => ['length' => ['max' => 64, 'min' => 1], 'required']
				]
			]
		];
	}

	public function alias() {
		return 'sts-city';
	}

	public function idFieldsNames() {
		return ['id_city'];
	}

}
