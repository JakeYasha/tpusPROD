<?php

/**
 * All data about existed geo points with goods and firms and other data.
 */
namespace App\Model;
class CurrentRegionCity extends \Sky4\Model\Composite {

	use Component\IdTrait;

	private $temp_table_mode = null;

	public function fields() {
		return [
			'id_country' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'Код',
				'params' => [
					'rules' => ['int']
				]
			],
			'id_region' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'Код',
				'params' => [
					'rules' => ['int']
				]
			],
			'id_city' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'Код города',
				'params' => [
					'rules' => ['int']
				]
			],
			'name' => [
				'elem' => 'text_field',
				'label' => 'Название',
				'params' => [
					'rules' => ['length' => ['max' => 255, 'min' => 1]]
				]
			],
			'count_goods' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'Кол-во товаров',
			],
			'count_goods_1' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'Кол-во товаров',
			],
			'count_goods_2' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'Кол-во услуг',
			],
			'count_goods_3' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'Кол-во оборудования',
			],
			'count_firms' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'Кол-во фирм',
			],
			'count_discounts' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'Кол-во скидочных предложений',
			],
			'count_videos' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'Кол-во видеороликов',
			]
		];
	}

	public function table() {
		if ($this->temp_table_mode === true) {
			return parent::table() . '_tmp';
		}
		return parent::table();
	}

	public function setTemporaryTableMode($mode) {
		$this->temp_table_mode = $mode;
	}

}
