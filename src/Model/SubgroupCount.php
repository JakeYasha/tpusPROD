<?php

namespace App\Model;

class SubgroupCount extends \Sky4\Model\Composite {

	use Component\IdTrait;

	public function fields() {
		return [
			'id_group' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null',
					'name' => 'id_group',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'id_group'
			],
			'id_subgroup' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null',
					'name' => 'id_subgroup',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'id_subgroup'
			],
			'id_city' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null',
					'name' => 'id_city',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_city'
			],
			'count_goods' => [
				'col' => [
					'default_val' => '0',
					'flags' => '',
					'name' => 'count_goods',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'count_goods'
			],
		];
	}

	public function getCurrentSubgroups($id_group = null) {
		$city_id_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
		$_where = [
			'AND',
			$city_id_conds['where']
		];

		$_params = $city_id_conds['params'];

		if ($id_group !== null) {
			$_where[] = '`id_group` = :id_group';
			$_params[':id_group'] = $id_group;
		} else {
			$_where[] = '`id_group` != :id_group22';
			$_where[] = '`id_group` != :id_group44';
			$_params[':id_group22'] = 22;
			$_params[':id_group44'] = 44;
		}

		return array_keys($this->reader()
						->setSelect(['DISTINCT(id_subgroup)'])
						->setWhere($_where, $_params)
						->rowsWithKey('id_subgroup'));
	}

}
