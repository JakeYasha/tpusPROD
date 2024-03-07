<?php

namespace App\Model;
class StsHistReaddress extends \Sky4\Model {

	public function fields() {
		return [
			'id_hist_readdress' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null primary_key',
					'name' => 'id_hist_readdress',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_hist_readdress'
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
			'readdress' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'readdress',
					'type' => 'string(64)',
				],
				'elem' => 'text_field',
				'label' => 'readdress'
			],
			'phone' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'phone',
					'type' => 'string(64)',
				],
				'elem' => 'text_field',
				'label' => 'phone'
			],
		];
	}

}
