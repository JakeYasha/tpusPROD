<?php

namespace App\Model\Component;

class IdFirm extends \Sky4\Model\Component {

	public function fields() {
		return array(
			'id_firm' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'autocomplete',
				'label' => 'ID фирмы',
				'params' => [
					'model_alias' => 'firm',
					'field_name' => 'company_name'
				]
			],
		);
	}

	public function formStructure() {
		return [
			['type' => 'field', 'name' => 'id_firm']
		];
	}

	public function title() {
		return 'ID фирмы';
	}

	public function getFirmNamesForFilter() {
		$result = [];
		$firms = [];

		$firms_ids = $this->model()->reader()
				->setSelect(['id_firm'])
				->rowsWithKey('id_firm');

		$firms_ids_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(array_keys($firms_ids), 'id');
		if ($firms_ids) {
			$f = new \App\Model\Firm();
			$firms = $f->reader()
					->setSelect(['id', 'id_firm', 'id_service', 'company_name'])
					->setWhere($firms_ids_conds['where'], $firms_ids_conds['params'])
					->setOrderBy('company_name ASC')
					->objects();
		}

		foreach ($firms as $frm) {
			$result[$frm->id()] = $frm->name();
		}

		return $result;
	}
}
