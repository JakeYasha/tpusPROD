<?php

namespace App\Model\Banner;

class CmsFilter extends \Sky4\Model\Filter {

	public function fields() {
		return $this->model()->getFilterFields();
	}

	public function structure() {
		return $this->model()->getFilterFormStructure();
	}

	public function assembleConds() {
		$_params = [];
		$_where = ['AND'];
		foreach ($this->fields as $field_name => $field_props) {
			if ($field_name === 'flag_is_active') {
				if (isset($field_props['val']) && (string) $field_props['val'] === 'on') {
					$_conds['params'][':now'] = \Sky4\Helper\DeprecatedDateTime::now();
					$_conds['params'][':flag'] = 1;
					$_conds['where'] = ['timestamp_beginning <= :now', 'timestamp_ending >= :now', 'flag_is_active = :flag'];
				}
			} else {
				$_conds = $this->assembleCondsByFieldProps($field_name, $field_props);
			}

			if (!empty($_conds['params']) && !empty($_conds['where'])) {
				$_params = array_merge($_params, $_conds['params']);
				$_where = array_merge($_where, $_conds['where']);
			}
		}

		if (empty($_params)) {
			$_where = array();
		}
		return array(
			'params' => $_params,
			'where' => $_where
		);
	}

}
