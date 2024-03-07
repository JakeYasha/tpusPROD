<?php

namespace App\Model\Component;

class GeoData extends \Sky4\Model\Component {

	private static $geoData = [];

	public function fields() {
		return array(
			'id_city' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'ID города',
				'params' => [
					'rules' => ['int']
				]
			],
			'id_country' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_2'
				],
				'elem' => 'text_field',
				'label' => 'ID страны',
				'params' => [
					'rules' => ['int']
				]
			],
			'id_region_country' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_2'
				],
				'elem' => 'text_field',
				'label' => 'ID района',
				'params' => [
					'rules' => ['int']
				]
			],
			'id_region_city' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_2'
				],
				'elem' => 'text_field',
				'label' => 'ID района города',
				'params' => [
					'rules' => ['int']
				]
			]
		);
	}

	public function formStructure() {
		return [
			['type' => 'field', 'name' => 'id_city'],
			['type' => 'field', 'name' => 'id_country'],
			['type' => 'field', 'name' => 'id_region_country'],
			['type' => 'field', 'name' => 'id_region_city']
		];
	}

	public function title() {
		return 'География';
	}

	private function loadGeoData() {
		if (!isset(self::$geoData[$this->model()->val('id_city')])) {
			$_select = [
				'`sts_country`.`id_country` as `id_country`',
				'`sts_country`.`name` as `country`',
				'`sts_country`.`code` as `countryCode`',
				'`current_region_city`.`id_region` as `id_region_country`',
				'`current_region_city`.`name` as `region`',
				'`sts_region_country`.`name` as `region_2`',
				'`sts_city`.`name` as `city`',
				'`sts_city`.`id_city_type` as `cityType`',
				'`sts_city`.`code` as `cityCode`'
			];

			$_join1 = [
				'AND',
				'`current_region_city`.`id_region` = :id_region_country',
				'`current_region_city`.`id_city` = :nil',
				'`current_region_city`.`id_country` = :id_country'
			];

			$_join1_params = [
				':id_region_country' => $this->model()->val('id_region_country'),
				':nil' => 0,
				':id_country' => $this->model()->val('id_country')
			];

			$_join2 = ['AND', '`sts_city`.`id_city` = :id_city'];

			$_join2_params = [':id_city' => $this->model()->val('id_city')];

			$_join3 = ['AND', '`sts_region_country`.`id_country` = :id_country', '`sts_region_country`.`id_region_country` = :id_region_country'];

			$_join3_params = [':id_country' => $this->model()->val('id_country'), ':id_region_country' => $this->model()->val('id_region_country')];

			$_where = '`sts_country`.`id_country` = :id_country';
			$_where_params = [':id_country' => $this->model()->val('id_country')];

			self::$geoData[$this->model()->val('id_city')] = app()->db()->query()
					->setSelect($_select)
					->setFrom(['sts_country'])
					->setLeftJoin('current_region_city', $_join1, $_join1_params)
					->setLeftJoin('sts_city', $_join2, $_join2_params)
					->setLeftJoin('sts_region_country', $_join3, $_join3_params)
					->setWhere($_where, $_where_params)
					->selectRow();

			if (!self::$geoData[$this->model()->val('id_city')]['region'] && self::$geoData[$this->model()->val('id_city')]['region_2']) {
				self::$geoData[$this->model()->val('id_city')]['region'] = self::$geoData[$this->model()->val('id_city')]['region_2'];
			}
		}

		return $this;
	}

	public function getGeoData() {
		$this->loadGeoData();
		return self::$geoData[$this->model()->val('id_city')];
	}

}
