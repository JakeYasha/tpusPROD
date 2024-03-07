<?php

namespace App\Model\Component;

class Source extends \Sky4\Model\Component {

	public function fields() {
		return [
			'source' => [
				'col' => [
					'default_val' => 'ratiss',
					'flags' => 'not_null',
					'type' => "list('ratiss','client','yml')"
				],
				'elem' => 'radio_buttons',
				'label' => 'Источник данных',
				'options' => ['ratiss' => 'РАТИСС', 'client' => 'Кабинет клиента', 'yml' => 'Загрузка из YML']
			]
		];
	}

}
