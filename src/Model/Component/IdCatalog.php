<?php

namespace App\Model\Component;

class IdCatalog extends \Sky4\Model\Component {

	public function fields() {
		return array(
			'id_catalog' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'autocomplete',
				'label' => 'ID каталога',
				'params' => [
					'model_alias' => 'price-catalog',
					'field_name' => 'name'
				]
			],
		);
	}

	public function formStructure() {
		return [
			['type' => 'field', 'name' => 'id_catalog']
		];
	}

	public function title() {
		return 'ID каталога';
	}

}
