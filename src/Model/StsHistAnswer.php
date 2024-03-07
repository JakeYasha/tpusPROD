<?php

namespace App\Model;
class StsHistAnswer extends \Sky4\Model {

	public function fields() {
		return [
			'id_hist_answer' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null primary_key',
					'name' => 'id_hist_answer',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_hist_answer'
			],
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
			'id_service' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'id_service',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'id_service'
			],
			'id_city' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'id_city',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_city'
			],
			'id_firm' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'id_firm',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_firm'
			],
			'id_price' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'id_price',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_price'
			],
			'id_group' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'id_group',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'id_group'
			],
			'id_subgroup' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'id_subgroup',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'id_subgroup'
			],
			'id_producer_country' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'id_producer_country',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'id_producer_country'
			],
			'id_producer_goods' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'id_producer_goods',
					'type' => 'int_1',
				],
				'elem' => 'text_field',
				'label' => 'id_producer_goods'
			],
			'datetime' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'datetime',
					'type' => 'date_time',
				],
				'elem' => 'text_field',
				'label' => 'datetime'
			],
			'manufacture' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'manufacture',
					'type' => 'string(64)',
				],
				'elem' => 'text_field',
				'label' => 'manufacture'
			],
			'unit' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'unit',
					'type' => 'string(32)',
				],
				'elem' => 'text_field',
				'label' => 'unit'
			],
			'pack' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'pack',
					'type' => 'string(32)',
				],
				'elem' => 'text_field',
				'label' => 'pack'
			],
			'name' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'name',
					'type' => 'string(256)',
				],
				'elem' => 'text_field',
				'label' => 'name'
			],
		];
	}

}
