<?php

namespace App\Model\Component;

class IdService extends \Sky4\Model\Component {

	public function fields() {
		return array(
			'id_service' => [
				'col' => [
					'flags' => 'not_null key unsigned',
					'type' => 'int_2'
				],
				'elem' => 'hidden_field',
				'label' => 'ID службы',
				'params' => [
					'rules' => ['int']
				]
			]
		);
	}

	public function formStructure() {
		return [
			['type' => 'field', 'name' => 'id_service']
		];
	}

	public function title() {
		return 'Служба';
	}

}
