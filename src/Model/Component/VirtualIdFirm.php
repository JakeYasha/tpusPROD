<?php

namespace App\Model\Component;

class VirtualIdFirm extends \Sky4\Model\Component {

	public function assembleFilterConds($field_props, \Sky4\Model\Filter $filter = null, $field_name = null) {
		$_conds['params'] = [];
		$_conds['where'] = [];
		if (isset($field_props['val']) && (string) $field_props['val'] !== '0') {
			$composite = explode('~', (string) $field_props['val']);
			if ($composite) {
				$_conds['where'] = ['id_firm = :id_firm', 'id_service = :id_service'];
				$_conds['params'] = [':id_firm' => $composite[0], ':id_service' => $composite[1]];
			}
		}

		return $_conds;
	}

	public function fields() {
		return [
			'virtual_id_firm' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'autocomplete',
				'label' => 'Название фирмы',
				'params' => [
					'rules' => ['required'],
					'model_alias' => 'firm',
					'field_name' => 'company_name',
				]
			],
		];
	}

	public function formStructure() {
		return [
			['type' => 'field', 'name' => 'virtual_id_firm']
		];
	}

	public function title() {
		return 'ID фирмы';
	}

	public function beforeInsert(&$vals) {
		if (isset($vals['virtual_id_firm']) && $vals['virtual_id_firm']) {
			$firm = new Firm();
			$firm->get($vals['virtual_id_firm']);
			if ($firm->exists()) {
				$vals['id_firm'] = $firm->id_firm();
				$vals['id_service'] = $firm->id_service();
				$fields = $this->model()->getFieldsNames();
				if (in_array('id_city', $fields)) {
					$vals['id_city'] = $firm->val('id_city');
				}
				$vals['virtual_id_firm'] = 0;
			}
		}

		return parent::beforeInsert();
	}

	public function beforeUpdate(&$vals) {
		if (isset($vals['virtual_id_firm']) && $vals['virtual_id_firm']) {
			$firm = new Firm();
			$firm->get($vals['virtual_id_firm']);
			if ($firm->exists()) {
				$vals['id_firm'] = $firm->id_firm();
				$vals['id_service'] = $firm->id_service();
				$fields = $this->model()->getFieldsNames();
				if (in_array('id_city', $fields)) {
					$vals['id_city'] = $firm->val('id_city');
				}
				$vals['virtual_id_firm'] = 0;
			}
		}

		return parent::beforeInsert();
	}

}
