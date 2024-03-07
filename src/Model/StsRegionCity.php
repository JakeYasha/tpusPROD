<?php

/**
 * Distrits. [Like Kirovskiy rayon. For Yaroslavl only]
 */
namespace App\Model;

class StsRegionCity extends \Sky4\Model {

	public function fields() {
		return [
			'id_region_city' => [
				'col' => [
					'flags' => 'not_null primary_key unsigned',
					'type' => 'int_1'
				],
				'elem' => 'text_field',
				'label' => 'Код',
				'params' => [
					'rules' => ['int', 'required']
				]
			],
			'id_city' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'Код страны',
				'params' => [
					'rules' => ['int', 'required']
				]
			],
			'name' => [
				'elem' => 'text_field',
				'label' => 'Название',
				'params' => [
					'rules' => ['length' => ['max' => 32, 'min' => 1], 'required']
				]
			]
		];
	}

	public function alias() {
		return 'sts-region-city';
	}

	public function idFieldsNames() {
		return ['id_region_city'];
	}

}
