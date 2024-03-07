<?php

namespace App\Model;

class StsHistCalls extends \Sky4\Model {

	public function fields() {
		return [
			'id_hist_calls' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null primary_key',
					'name' => 'id_hist_calls',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_hist_calls'
			],
			'from_id_service' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null primary_key',
					'name' => 'from_id_service',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'from_id_service'
			],
			'datetime' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'datetime',
					'type' => 'date_time',
				],
				'elem' => 'text_field',
				'label' => 'datetime'
			],
			'datetime_finish' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'datetime_finish',
					'type' => 'date_time',
				],
				'elem' => 'text_field',
				'label' => 'datetime_finish'
			]
		];
	}

}
