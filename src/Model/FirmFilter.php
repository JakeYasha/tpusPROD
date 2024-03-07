<?php

namespace App\Model;

class FirmFilter extends \Sky4\Model\Filter {

	public function assembleConds() {
		$_params = [];
		$_where = ['AND'];
		foreach ($this->fields as $field_name => $field_props) {
			if ($field_name === 'id_firm' && (int) $field_props['val'] === 0) {
				continue;
			}
			if (isset($field_props['assembler']) && isset($field_props['assembler']['class_name']) && isset($field_props['assembler']['method_name'])) {
				$_conds = \Sky4\Helper\CustomObject::execute($field_props['assembler']['class_name'], $field_props['assembler']['method_name'], [$field_props, $this, $field_name]);
			} else {
				$_conds = $this->assembleCondsByFieldProps($field_name, $field_props);
			}
			if ($_conds['params'] && $_conds['where']) {
				$_params = array_merge($_params, $_conds['params']);
				$_where = array_merge($_where, $_conds['where']);
			}
		}
		
		if (!$_params) {
			$_where = [];
		}
		return [
			'params' => $_params,
			'where' => $_where
		];
	}

}
