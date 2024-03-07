<?php

namespace App\Model;

class FirmCity extends \Sky4\Model\Composite {

	public function idFieldsNames() {
		return ['id_firm', 'id_city'];
	}

	public function fields() {
		$fc = $this->fieldPropCreator();
		return [
			'id_firm' => $fc->intField(),
			'id_city' => $fc->intField(),
		];
	}

}
