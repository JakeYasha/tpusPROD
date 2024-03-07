<?php

namespace App\Model;
class FirmPromoCatalog extends \Sky4\Model\Composite {

	public function fields() {
		return [
			'firm_promo_id' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'ID акции',
				'params' => [
					'rules' => ['int']
				]
			],
			
			'price_catalog_id' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'ID каталога',
				'params' => [
					'rules' => ['int']
				]
			]
		];
	}
	
	public function idFieldsNames() {
		return ['firm_promo_id', 'price_catalog_id'];
	}
}