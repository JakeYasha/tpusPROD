<?php

namespace App\Model\Component;

class IdPrice extends \Sky4\Model\Component {

	public function fields() {
		return array(
			'id_price' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'autocomplete',
				'label' => 'ID товара',
				'params' => [
					'model_alias' => 'price',
					'field_name' => 'name'
				]
			],
		);
	}

	public function formStructure() {
		return [
			['type' => 'field', 'name' => 'id_price']
		];
	}

	public function title() {
		return 'ID товара';
	}

}
