<?php
/**
 * Countries.
 */

namespace App\Model;
class StsCountry extends \Sky4\Model {

	public function fields() {
		return [
			'id_country' => [
				'col' => [
					'flags' => 'not_null index unsigned',
					'type' => 'int_2'
				],
				'elem' => 'text_field',
				'label' => 'Код',
				'params' => [
					'rules' => ['int', 'required']
				]
			],
			'code' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_2'
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
					'rules' => ['length' => ['max' => 64, 'min' => 1], 'required']
				]
			]
		];
	}

	public function alias() {
		return 'sts-country';
	}
	
	public function idFieldsNames() {
		return ['id_country'];
	}

}
