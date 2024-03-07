<?php

namespace App\Model;
class StsReklInfo extends \Sky4\Model\Composite {
	
	public function idFieldsNames() {
		return ['id_rekl_info'];
	}

	public function fields() {
		return [
			'id_group' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null primary_key',
					'name' => 'id_group',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'id_group'
			],
			'id_subgroup' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null primary_key',
					'name' => 'id_subgroup',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'id_subgroup'
			],
			'info' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'info',
					'type' => 'string(255)',
				],
				'elem' => 'text_field',
				'label' => 'info'
			],
			'id_rekl_info' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null',
					'name' => 'id_rekl_info',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_rekl_info'
			],
		];
	}

}
