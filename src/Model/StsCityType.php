<?php

namespace App\Model;
class StsCityType extends \Sky4\Model {

	public function fields() {
		return [
			'id_city_type' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_2'
				],
				'elem' => 'text_field',
				'label' => 'Код',
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

	public function idFieldsNames() {
		return ['id_city_type'];
	}

}
