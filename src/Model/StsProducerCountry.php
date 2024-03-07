<?php

namespace App\Model;

class StsProducerCountry extends \Sky4\Model\Composite {

	public function fields() {
		return [
			'id_producer_country' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null',
					'name' => 'id_producer_country',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'id_producer_country'
			],
			'name' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'name',
					'type' => 'string(36)',
				],
				'elem' => 'text_field',
				'label' => 'name'
			],
		];
	}

	public function idFieldsNames() {
		return ['id_producer_country'];
	}

}
